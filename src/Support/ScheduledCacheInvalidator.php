<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Support;

use Carbon\Carbon;
use Statamic\Support\Arr;
use Statamic\Facades\Entry;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as CollectionFacade;
use MityDigital\StatamicScheduledCacheInvalidator\Scopes\Now;

class ScheduledCacheInvalidator
{
    public function getEntries(): \Illuminate\Support\Collection
    {
        // what is "now"? how existential...
        $now = Carbon::now();

        return CollectionFacade::all()
            ->filter(fn ($collection) => $this->scopes($collection)->isNotEmpty())
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

    protected function scopes(Collection $collection): \Illuminate\Support\Collection
    {
        $scopes = Arr::wrap(config('statamic-scheduled-cache-invalidator.query_scopes'));

        if (Arr::isAssoc($scopes)) {
            $scopes = Arr::wrap(Arr::get($scopes, $collection->handle()));
        }

        if ($collection->dated()) {
            $scopes[] = Now::class;
        }

        return collect($scopes)->map(fn ($scope) => app($scope));
    }
}
