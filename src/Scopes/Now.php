<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Scopes;

use Statamic\Query\Scopes\Scope;

class Now extends Scope
{
    public function apply($query, $values)
    {
        $field = $values['collection']->sortField() ?? 'date';

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
}
