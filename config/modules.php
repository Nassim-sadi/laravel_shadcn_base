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

    'services'       => env('MODULE_SERVICES', true),
    'projects'       => env('MODULE_PROJECTS', true),
    'testimonials'   => env('MODULE_TESTIMONIALS', true),
    'faqs'           => env('MODULE_FAQS', true),
    'media'          => env('MODULE_MEDIA', true),
    'contact'        => env('MODULE_CONTACT', true),
    'email_templates' => env('MODULE_EMAIL_TEMPLATES', true),
    'activity_logs'  => env('MODULE_ACTIVITY_LOGS', true),
    'translations'   => env('MODULE_TRANSLATIONS', true),

    'catalog' => env('MODULE_CATALOG', false),
    'booking' => env('MODULE_BOOKING', false),
    'blog'    => env('MODULE_BLOG', false),
];
