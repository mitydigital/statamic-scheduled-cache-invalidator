<?php

use MityDigital\StatamicScheduledCacheInvalidator\Scopes\Now;
use Statamic\Facades\Collection;

it('returns the correct sort field for a dated collection', function () {
    $scope = app(Now::class);
    $collection = Collection::findByHandle('dated');
    expect($scope->getSortField($collection))->toBeString()->toBe('date');
});

it('returns the correct sort field for a dated and orderable collection', function () {
    $scope = app(Now::class);
    $collection = Collection::findByHandle('dated_and_orderable');
    expect($scope->getSortField($collection))->toBeString()->toBe('date');
});

it('returns the correct sort field for a dated and timed collection', function () {
    $scope = app(Now::class);
    $collection = Collection::findByHandle('dated_and_timed');
    expect($scope->getSortField($collection))->toBeString()->toBe('date');
});

it('returns the correct sort field for a dated and timed other collection', function () {
    $scope = app(Now::class);
    $collection = Collection::findByHandle('dated_and_timed_other');
    expect($scope->getSortField($collection))->toBeString()->toBe('date');
});

it('returns the correct sort field for a ordered collection', function () {
    $scope = app(Now::class);

    // max depth = 1
    $collection = Collection::findByHandle('ordered');
    expect($scope->getSortField($collection))->toBeString()->toBe('order');

    // max depth != 1
    $collection = Collection::findByHandle('ordered_depth');
    expect($scope->getSortField($collection))->toBeString()->toBe('title');
});

it('returns the correct sort field for an undated collection', function () {
    $scope = app(Now::class);
    $collection = Collection::findByHandle('undated');
    expect($scope->getSortField($collection))->toBeString()->toBe('title');
});
