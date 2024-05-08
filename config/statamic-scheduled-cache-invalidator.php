<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    |
    | You can provide collection-specific Query Scopes to tweak the behaviour
    | of the logic that gets entries that need to be invalidated.
    |
    | This can be an array of collection-specific Query Scopes for any
    | collection that you need, or simply pass the reference to your query
    | scope to apply to any collection.
    |
    */

    'query_scopes' => null,

    // 'query_scopes' => \Path\To\Scope::class,

    // 'query_scopes' => [
    //     \Path\To\Scope::class,
    //     \Path\To\DifferentScope::class,
    // ],

    // 'query_scopes' => [
    //     'collection_handle' => \Path\To\Scope::class,
    //     'another_collection' => \Path\To\DifferentScope::class,
    // ],

    // 'query_scopes' => [
    //     'collection_handle' => [
    //         \Path\To\Scope::class,
    //         \Path\To\DifferentScope::class,
    //     ],
    //     'another_collection' => [
    //         \Path\To\Scope::class,
    //         \Path\To\DifferentScope::class,
    //     ],
    // ],

    /*
    |--------------------------------------------------------------------------
    | Save quietly
    |--------------------------------------------------------------------------
    |
    | Do you want to save items quietly (true, so no events fired), or
    | not quietly (false, events will be fired)
    |
    */

    'save_quietly' => true,

    /*
    |--------------------------------------------------------------------------
    | Now scope behaviour
    |--------------------------------------------------------------------------
    |
    | An array of collection handles and the field handle to use as the
    | "field" to use in the Now scope for date behaviour.
    |
    | You only need to specify collections that need overriding - otherwise
    | the sortField (or 'date') will be used. All blueprints in the collection
    | must have this field.
    |
    */

    'now_fields' => [],

    // 'now_fields' => [
    //     'collection_handle' => 'another_date',
    //     'another_collection' => 'processed_date'
    // ]

];
