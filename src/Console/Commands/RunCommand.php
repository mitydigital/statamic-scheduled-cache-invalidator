<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Console\Commands;

use Illuminate\Console\Command;
use MityDigital\StatamicScheduledCacheInvalidator\Support\ScheduledCacheInvalidator;
use Statamic\Console\RunsInPlease;
use Statamic\Entries\Collection;
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
        $collections = $support->getCollections();

        // do we have entries?
        if ($collections->count()) {
            // let's make the invalidator
            $invalidator = app(Invalidator::class);

            // let's communicate - single or plural
            $this->info(__($collections->count() === 1 ? 'statamic-scheduled-cache-invalidator::command.collections.single' : 'statamic-scheduled-cache-invalidator::command.collections.count',
                [
                    'count' => $collections->count(),
                ]));

            // let's clear the cache for each Collection's mount
            $collections->each(function (Collection $collection, $index) use ($invalidator) {
                // invalidate magic!
                $invalidator->invalidate($collection);

                // progress output
                $this->line(__('statamic-scheduled-cache-invalidator::command.collections.invalidated', [
                    'collection' => $collection->title(),
                ]));
            });

            $this->info(__('statamic-scheduled-cache-invalidator::command.completed'));
        } else {
            // nothing to do, let's finish this up
            $this->info(__('statamic-scheduled-cache-invalidator::command.collections.none'));
        }
    }
}
