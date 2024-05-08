<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Scopes;

use Exception;
use Statamic\Entries\Collection;
use Statamic\Query\Scopes\Scope;

class Now extends Scope
{
    public function apply($query, $values)
    {
        // set the collection
        $collection = $values['collection'];

        // get the sort field
        $field = $this->getSortField($collection);

        // configure the overrides
        $overrides = config('statamic-scheduled-cache-invalidator.now_fields', []);
        if (is_array($overrides) && array_key_exists($collection->handle(), $overrides)) {
            $field = $overrides[$collection->handle()];
        }

        // build the query
        $query
            ->whereDate($field, $values['now']->format('Y-m-d'))
            ->where(function ($query) use ($field, $values) {
                $query->where(function ($query) use ($field, $values) {
                    $query
                        ->whereTime($field, '>=', $values['now']->format('H:i').':00')
                        ->whereTime($field, '<=', $values['now']->format('H:i').':59');
                })
                    ->orWhereTime($field, $values['now']->format('H:i'));
            });
    }

    public function getSortField(Collection $collection): string
    {
        if (! $collection) {
            throw new Exception('Collection not configured');
        }

        $field = $collection->sortField() ?? 'date';

        // force 'date' for any dated collection - statamic always goes back to the 'order' field
        // for a re-orderable collection, so this forces it back to 'date'
        if ($collection->dated()) {
            $field = 'date';
        }

        return $field;
    }
}
