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
            ->whereTime($field, $values['now']->format('H:i').':00');
    }
}
