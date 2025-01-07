<?php

/**
 * Returns the import map for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "preload" set to true for any modules that are loaded on the initial
 *     page load to help the browser download them earlier.
 *
 * The "importmap:require" command can be used to add new entries to this file.
 *
 * This file has been auto-generated by the importmap commands.
 */
return [
    'app' => [
        'path' => 'app.js',
        'preload' => true,
    ],
    'room' => [
        'path' => 'js/room.js',
        'entrypoint' => true,
    ],

    'plan' => [

        'path' => 'js/plan.js',
        'entrypoint' => true,
    ],
    
    'graphdetail' => [
        'path' => 'js/graphdetail.js',
        'entrypoint' => true,
    ],
    'carouseltechnician' => [
    'path' => 'js/carouseltechnician.js',
    'entrypoint' => true,
    ]

];
