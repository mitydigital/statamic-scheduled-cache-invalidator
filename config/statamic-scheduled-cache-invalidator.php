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

];
