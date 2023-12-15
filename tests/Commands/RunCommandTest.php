<?php

use MityDigital\StatamicScheduledCacheInvalidator\Console\Commands\RunCommand;
use MityDigital\StatamicScheduledCacheInvalidator\Support\ScheduledCacheInvalidator;
use Mockery\MockInterface;
use Statamic\Facades\Collection;
use Statamic\StaticCaching\Invalidator;

beforeEach(function () {
    $this->command = app(RunCommand::class);
});

it('has the correct signature', function () {
    $signature = getPrivateProperty(RunCommand::class, 'signature');

    expect($signature->getValue($this->command))->toBe('statamic:scheduled-cache-invalidator:run');
});

it('returns the correct message when static caching disabled', function () {
    config(['statamic.static_caching.strategy' => null]);

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('Static Caching is disabled: there is nothing for the Scheduled Caching Invalidator to do.')
        ->assertExitCode(0);
});

it('returns the correct message when static caching enabled', function () {

    // return nothing
    $this->partialMock(ScheduledCacheInvalidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('getCollections')
            ->andReturn(collect([]));
    });

    // try with half caching
    config(['statamic.static_caching.strategy' => 'half']);

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('There is nothing for the Scheduled Caching Invalidator to do.')
        ->assertExitCode(0);

    // try with full caching
    config(['statamic.static_caching.strategy' => 'full']);

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('There is nothing for the Scheduled Caching Invalidator to do.')
        ->assertExitCode(0);
});

it('invalidates the cache for a single collection', function () {
    // enable caching
    config(['statamic.static_caching.strategy' => 'full']);

    // get a single collection
    $collection = Collection::findByHandle('dated');

    $this->partialMock(ScheduledCacheInvalidator::class, function (MockInterface $mock) use ($collection) {
        $mock->shouldReceive('getCollections')
            ->once()
            ->andReturn(collect([
                $collection,
            ]));
    });

    $this->partialMock(Invalidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('invalidate')->once();
    });

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('Scheduled Cache Invalidator found 1 Collection to invalidate:')
        ->expectsOutput('Static Cache invalidated for "'.$collection->title().'"')
        ->expectsOutput('Scheduled Cache Invalidator has finished.')
        ->assertExitCode(0);
});

it('invalidates the cache for multiple collection', function () {
    // enable caching
    config(['statamic.static_caching.strategy' => 'full']);

    // get two collections
    $collection = Collection::findByHandle('dated');
    $collection2 = Collection::findByHandle('dated_and_timed');

    $this->partialMock(ScheduledCacheInvalidator::class,
        function (MockInterface $mock) use ($collection, $collection2) {
            $mock->shouldReceive('getCollections')
                ->once()
                ->andReturn(collect([
                    $collection,
                    $collection2,
                ]));
        });

    $this->partialMock(Invalidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('invalidate')->twice();
    });

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('Scheduled Cache Invalidator found 2 Collections to invalidate:')
        ->expectsOutput('Static Cache invalidated for "'.$collection->title().'"')
        ->expectsOutput('Static Cache invalidated for "'.$collection2->title().'"')
        ->expectsOutput('Scheduled Cache Invalidator has finished.')
        ->assertExitCode(0);
});
