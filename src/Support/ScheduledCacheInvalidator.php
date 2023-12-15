<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Support;

use Carbon\Carbon;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry;

class ScheduledCacheInvalidator
{
    public function getCollections(): \Illuminate\Support\Collection
    {
        // what is "now"? how existential...
        $now = Carbon::now()->format('Y-m-d H:i');

        // get the collections that have a "date" (or whatever the collection is configured for) where an entry
        // is due to be published this minute
        return CollectionFacade::all()
            ->filter(fn (Collection $collection) => $collection->dated() && Entry::query()
                ->where('collection', $collection->handle())
                ->where('published', true)
                ->whereTime($collection->sortField() ?? 'date', $now)
                ->count())
            ->flatten();
    }
}
