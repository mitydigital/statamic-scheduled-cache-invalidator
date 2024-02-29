<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Support;

use Carbon\Carbon;
use Statamic\Support\Arr;
use Statamic\Facades\Entry;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as CollectionFacade;
use MityDigital\StatamicScheduledCacheInvalidator\Scopes\DateIsPast;
use MityDigital\StatamicScheduledCacheInvalidator\Scopes\NullScope;

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
                return Entry::query()
                    ->where('collection', $collection->handle())
                    ->where('published', true)
                    ->where(fn ($query) =>  $this->scopes($collection)->each->apply($query, ['collection' => $collection, 'now' => $now]))
                    ->get();
            })
            ->flatten()
            ->each->saveQuietly();
    }

    protected function scopes($collection): \Illuminate\Support\Collection
    {
        $scopes = collect();

        if ($collection->dated()) {
            $scopes->push(DateIsPast::class);
        }

        $scope = config('statamic-scheduled-cache-invalidator.query_scopes', null);

        $scope = is_array($scope)
            ? Arr::get($scope, $collection->handle())
            : $scope;

        if ($scope) {
            $scopes->push($scope);
        }

        return $scopes->map(fn ($scope) => app($scope));
    }
}
