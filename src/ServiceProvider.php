<?php

namespace MityDigital\StatamicScheduledCacheInvalidator;

use MityDigital\StatamicScheduledCacheInvalidator\Console\Commands\RunCommand;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        RunCommand::class,
    ];

    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/statamic-scheduled-cache-invalidator.php', 'statamic-scheduled-cache-invalidator');

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/statamic-scheduled-cache-invalidator.php' => config_path('statamic-scheduled-cache-invalidator.php'),
            ], 'statamic-scheduled-cache-invalidator');

        }
    }
}
