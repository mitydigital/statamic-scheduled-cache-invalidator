<?php

namespace MityDigital\StatamicScheduledCacheInvalidator;

use MityDigital\StatamicScheduledCacheInvalidator\Console\Commands\RunCommand;
use Statamic\Providers\AddonServiceProvider;
use Statamic\StaticCaching\Invalidator;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        RunCommand::class,
    ];

    public function bootAddon()
    {
        $invalidator = app(Invalidator::class);
        //dd($invalidator);
        parent::bootAddon();
    }
}
