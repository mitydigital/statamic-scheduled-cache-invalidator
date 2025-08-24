<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Console\Commands;

use Illuminate\Console\Command;
use MityDigital\StatamicScheduledCacheInvalidator\Support\ScheduledCacheInvalidator;
use Statamic\Console\RunsInPlease;
use Statamic\Contracts\Entries\Entry;
use Statamic\StaticCaching\Invalidator;

class RunCommand extends Command
{
    use RunsInPlease;

    protected $signature = 'statamic:scheduled-cache-invalidator:run';

    protected $description = 'Invalidates cached content for Entries with a publish date in this minute.';

    public function handle(): void
    {
        // is caching even enabled?
        if (! config('statamic.static_caching.strategy', null)) {
            $this->comment(__('statamic-scheduled-cache-invalidator::command.disabled'));

            return;
        }

        // this far, we have a non-null caching strategy
        // let's go!

        $support = app(ScheduledCacheInvalidator::class);

        // get some entries
        $entries = $support->getEntries();

        // do we have entries?
        if ($entries->count()) {
            // let's make the invalidator
            $invalidator = app(Invalidator::class);

            // let's communicate - single or plural
            $this->info(__($entries->count() === 1 ? 'statamic-scheduled-cache-invalidator::command.entries.single'
                : 'statamic-scheduled-cache-invalidator::command.entries.count',
                [
                    'count' => $entries->count(),
                ]));

            // let's clear the cache for each Entry
            $entries->each(function (Entry $entry, $index) use ($invalidator) {
                // if we have saved normally, invalidation has already happened
                if (config('statamic-scheduled-cache-invalidator.save_quietly', true)) {
                    // invalidate magic!
                    $invalidator->invalidate($entry);
                }

                // progress output
                $this->line(__('statamic-scheduled-cache-invalidator::command.entries.invalidated', [
                    'id' => $entry->id(),
                ]));
            });

            $this->info(__('statamic-scheduled-cache-invalidator::command.completed'));
        } else {
            // nothing to do, let's finish this up
            $this->info(__('statamic-scheduled-cache-invalidator::command.entries.none'));
        }
    }
}
