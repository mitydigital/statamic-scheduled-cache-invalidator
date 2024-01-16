<?php

use MityDigital\StatamicScheduledCacheInvalidator\Support\ScheduledCacheInvalidator;
use Mockery\MockInterface;
use Statamic\Query\Scopes\Scope;

use function Spatie\PestPluginTestTime\testTime;

it('correctly gets an entry when time is disabled for the collection', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be BEFORE publish
    testTime()->freeze('2023-12-14 23:59:00');

    expect($support->getEntries())->toHaveCount(0);

    // freeze time to be ON publish
    testTime()->freeze('2023-12-15 00:00:00');

    $entries = $support->getEntries();

    // should have 1, and the "dated_and_timed" collection
    expect($entries)->toHaveCount(1)
        ->and($entries->first()->collection()->handle())->toBe('dated');

    // freeze time to be AFTER publish
    testTime()->freeze('2023-12-15 00:01:00');

    expect($support->getEntries())->toHaveCount(0);
});

it('correctly gets an entry when time is enabled for the collection', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be BEFORE publish
    testTime()->freeze('2023-12-15 11:55:00');

    expect($support->getEntries())->toHaveCount(0);

    // freeze time to be ON publish
    testTime()->freeze('2023-12-15 11:56:00');

    $entries = $support->getEntries();

    // should have 1, and the "dated_and_timed" collection
    expect($entries)->toHaveCount(1)
        ->and($entries->first()->collection()->handle())->toBe('dated_and_timed');

    // freeze time to be AFTER publish
    testTime()->freeze('2023-12-15 11:57:00');

    expect($support->getEntries())->toHaveCount(0);
});

it('does not return entried from an undated collection', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be ON publish - this is when the undated entry has a "date" param
    testTime()->freeze('2023-12-07 00:00:00');

    // should have nothing returned - it's not dated
    expect($support->getEntries())->toHaveCount(0);
});

it('supports query scopes', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be ON publish - this is when the undated entry has a "date" param
    testTime()->freeze('2023-12-07 00:00:00');

    config()->set('statamic-scheduled-cache-invalidator.query_scopes', TestScope::class);

    $this->partialMock(TestScope::class, function (MockInterface $mock) {
        $mock->shouldReceive('apply')->times(2);
    });

    // should have nothing returned - it's not dated
    expect($support->getEntries())->toHaveCount(0);
});

it('supports query scopes for a collection but set up as an array', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be ON publish - this is when the undated entry has a "date" param
    testTime()->freeze('2023-12-07 00:00:00');

    config()->set('statamic-scheduled-cache-invalidator.query_scopes.dated_and_timed', AnotherTestScope::class);

    $this->partialMock(TestScope::class, function (MockInterface $mock) {
        $mock->shouldReceive('apply')->times(0);
    });

    $this->partialMock(AnotherTestScope::class, function (MockInterface $mock) {
        $mock->shouldReceive('apply')->times(1);
    });

    // should have nothing returned - it's not dated
    expect($support->getEntries())->toHaveCount(0);
});

it('supports query scopes for collections', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be ON publish - this is when the undated entry has a "date" param
    testTime()->freeze('2023-12-07 00:00:00');

    config()->set('statamic-scheduled-cache-invalidator.query_scopes.dated', TestScope::class);
    config()->set('statamic-scheduled-cache-invalidator.query_scopes.dated_and_timed', AnotherTestScope::class);

    $this->partialMock(TestScope::class, function (MockInterface $mock) {
        $mock->shouldReceive('apply')->times(1);
    });

    $this->partialMock(AnotherTestScope::class, function (MockInterface $mock) {
        $mock->shouldReceive('apply')->times(1);
    });

    // should have nothing returned - it's not dated
    expect($support->getEntries())->toHaveCount(0);
});

class TestScope extends Scope
{
    public function apply($query, $params)
    {
    }
}

class AnotherTestScope extends Scope
{
    public function apply($query, $params)
    {
    }
}
