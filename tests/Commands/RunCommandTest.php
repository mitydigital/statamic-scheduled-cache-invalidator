<?php

use MityDigital\StatamicScheduledCacheInvalidator\Console\Commands\RunCommand;
use MityDigital\StatamicScheduledCacheInvalidator\Support\ScheduledCacheInvalidator;
use Mockery\MockInterface;
use Statamic\Facades\Entry;
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
        $mock->shouldReceive('getEntries')
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

it('invalidates the cache for a single entry', function () {
    // enable caching
    config(['statamic.static_caching.strategy' => 'full']);

    // get a single entry
    $entry = Entry::make()->id('test');

    $this->partialMock(ScheduledCacheInvalidator::class, function (MockInterface $mock) use ($entry) {
        $mock->shouldReceive('getEntries')
            ->once()
            ->andReturn(collect([
                $entry,
            ]));
    });

    $this->partialMock(Invalidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('invalidate')->once();
    });

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('Scheduled Cache Invalidator found 1 Entry to invalidate:')
        ->expectsOutput('Static Cache invalidated for Entry "'.$entry->id().'"')
        ->expectsOutput('Scheduled Cache Invalidator has finished.')
        ->assertExitCode(0);
});

it('invalidates the cache for multiple entries', function () {
    // enable caching
    config(['statamic.static_caching.strategy' => 'full']);

    // get two entries
    $entry1 = Entry::make()->id('test1');
    $entry2 = Entry::make()->id('test2');

    $this->partialMock(ScheduledCacheInvalidator::class,
        function (MockInterface $mock) use ($entry1, $entry2) {
            $mock->shouldReceive('getEntries')
                ->once()
                ->andReturn(collect([
                    $entry1,
                    $entry2,
                ]));
        });

    $this->partialMock(Invalidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('invalidate')->twice();
    });

    $this->artisan('statamic:scheduled-cache-invalidator:run')
        ->expectsOutput('Scheduled Cache Invalidator found 2 Entries to invalidate:')
        ->expectsOutput('Static Cache invalidated for Entry "'.$entry1->id().'"')
        ->expectsOutput('Static Cache invalidated for Entry "'.$entry2->id().'"')
        ->expectsOutput('Scheduled Cache Invalidator has finished.')
        ->assertExitCode(0);
});
