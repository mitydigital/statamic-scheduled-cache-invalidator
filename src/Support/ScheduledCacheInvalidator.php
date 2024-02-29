<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Support;

use Carbon\Carbon;
use Statamic\Support\Arr;
use Statamic\Facades\Entry;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as CollectionFacade;
use MityDigital\StatamicScheduledCacheInvalidator\Scopes\DateIsPast;

class ScheduledCacheInvalidator
{
    public function getEntries(): \Illuminate\Support\Collection
    {
        // what is "now"? how existential...
        $now = Carbon::now();

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
                        app(DateIsPast::class)->apply($query, ['collection' => $collection, 'now' => $now]);

                        // what scope do we want to use?
                        $scope = config('statamic-scheduled-cache-invalidator.query_scopes', null);

                        // are we an array of collections?
                        if (is_array($scope)) {
                            // get the scope from the array
                            $scope = Arr::get($scope, $collection->handle(), null);
                        }

                        // if we have a scope, apply it
                        if ($scope) {
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
