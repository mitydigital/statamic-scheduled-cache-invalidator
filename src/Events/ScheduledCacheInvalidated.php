<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ScheduledCacheInvalidated
{
    use Dispatchable;

    public function __construct(
        public array $collections,
    ) {
    }
}
