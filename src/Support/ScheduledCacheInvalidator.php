<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Support;

use Carbon\Carbon;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry;

class ScheduledCacheInvalidator
{
    public function getEntries(): \Illuminate\Support\Collection
    {
        // what is "now"? how existential...
        $now = Carbon::now()->format('Y-m-d H:i');

        // get the entries inside a dated collection
        // that have a "date" (or whatever the collection is configured for)
        // due to be published this minute
        return CollectionFacade::all()
            ->filter(fn (Collection $collection) => $collection->dated())
            ->map(function (Collection $collection) use ($now) {
                $entries = Entry::query()
                    ->where('collection', $collection->handle())
                    ->where('published', true)
                    ->where(function ($query) use ($collection, $now) {
                        $query->whereTime($collection->sortField() ?? 'date', $now);

                        if ($scope = config('statamic-scheduled-cache-invalidator.query_scopes.'.$collection->handle(), config('statamic-scheduled-cache-invalidator.query_scopes.all'))) {
                            app($scope)->apply($query, ['collection' => $collection, 'now' => $now]);
                        }
                    })
                    ->get();

                // this triggers any publish status changes in the stache
                $entries->each->saveQuietly();

                return $entries;
            })
            ->filter()
            ->flatten();
    }
}
