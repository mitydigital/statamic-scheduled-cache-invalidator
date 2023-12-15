<?php

use MityDigital\StatamicScheduledCacheInvalidator\Support\ScheduledCacheInvalidator;

use function Spatie\PestPluginTestTime\testTime;

it('correctly gets a collection when time is disabled for the collection', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be BEFORE publish
    testTime()->freeze('2023-12-14 23:59:00');

    expect($support->getCollections())->toHaveCount(0);

    // freeze time to be ON publish
    testTime()->freeze('2023-12-15 00:00:00');

    $collections = $support->getCollections();

    // should have 1, and the "dated_and_timed" collection
    expect($collections)->toHaveCount(1)
        ->and($collections->first()->handle())->toBe('dated');

    // freeze time to be AFTER publish
    testTime()->freeze('2023-12-15 00:01:00');

    expect($support->getCollections())->toHaveCount(0);
});

it('correctly gets a collection when time is enabled for the collection', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be BEFORE publish
    testTime()->freeze('2023-12-15 11:55:00');

    expect($support->getCollections())->toHaveCount(0);

    // freeze time to be ON publish
    testTime()->freeze('2023-12-15 11:56:00');

    $collections = $support->getCollections();

    // should have 1, and the "dated_and_timed" collection
    expect($collections)->toHaveCount(1)
        ->and($collections->first()->handle())->toBe('dated_and_timed');

    // freeze time to be AFTER publish
    testTime()->freeze('2023-12-15 11:57:00');

    expect($support->getCollections())->toHaveCount(0);
});

it('does not return an undated collection', function () {
    // get the support
    $support = app(ScheduledCacheInvalidator::class);

    // freeze time to be ON publish - this is when the undated entry has a "date" param
    testTime()->freeze('2023-12-07 00:00:00');

    // should have nothing returned - it's not dated
    expect($support->getCollections())->toHaveCount(0);
});
