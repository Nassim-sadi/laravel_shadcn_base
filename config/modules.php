<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Optional Modules
    |--------------------------------------------------------------------------
    |
    | Toggle optional modules on/off per project.
    | When disabled, routes are not registered and sidebar items are hidden.
    |
    | To enable a module, set it to true in your .env file:
    |   MODULE_CATALOG=true
    |
    */

    'catalog' => env('MODULE_CATALOG', false),
    'booking' => env('MODULE_BOOKING', false),
    'blog'    => env('MODULE_BLOG', false),
];
