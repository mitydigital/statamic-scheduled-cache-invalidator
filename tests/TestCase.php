<?php

namespace MityDigital\StatamicScheduledCacheInvalidator\Tests;

use Facades\Statamic\Version;
use Illuminate\Encryption\Encrypter;
use MityDigital\StatamicScheduledCacheInvalidator\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Console\Processes\Composer;
use Statamic\Extend\Manifest;
use Statamic\Facades\Blueprint;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    protected $shouldFakeVersion = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        if ($this->shouldFakeVersion) {
            Version::shouldReceive('get')
                ->andReturn(Composer::create(__DIR__.'/../')->installedVersion(Statamic::PACKAGE));
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'mitydigital/statamic-scheduled-cache-invalidator' => [
                'id' => 'mitydigital/statamic-scheduled-cache-invalidator',
                'namespace' => 'MityDigital\\StatamicScheduledCacheInvalidator',
            ],
        ];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'stache',
            'static_caching',
            'users',
        ];

        foreach ($configs as $config) {
            $app['config']->set(
                "statamic.$config",
                require (__DIR__."/../vendor/statamic/cms/config/{$config}.php")
            );
        }

        $app['config']->set('app.key', 'base64:'.base64_encode(
            Encrypter::generateKey($app['config']['app.cipher'])
        ));

        // content
        $app['config']->set('statamic.stache.stores.collections.directory',
            __DIR__.'/__fixtures__/content/collections');
        $app['config']->set('statamic.stache.stores.entries.directory',
            __DIR__.'/__fixtures__/content/collections');
        $app['config']->set('statamic.stache.stores.collection-trees.directory',
            __DIR__.'/__fixtures__/content/trees/collections');

        $app['config']->set('auth.providers.users.driver', 'statamic');
        $app['config']->set('statamic.users.repository', 'file');

        Statamic::booted(function () {
            Blueprint::setDirectory(__DIR__.'/__fixtures__/blueprints');
        });
    }
}
