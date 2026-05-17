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

    'services'       => filter_var(env('MODULE_SERVICES', true), FILTER_VALIDATE_BOOLEAN),
    'projects'       => filter_var(env('MODULE_PROJECTS', true), FILTER_VALIDATE_BOOLEAN),
    'testimonials'   => filter_var(env('MODULE_TESTIMONIALS', true), FILTER_VALIDATE_BOOLEAN),
    'faqs'           => filter_var(env('MODULE_FAQS', true), FILTER_VALIDATE_BOOLEAN),
    'media'          => filter_var(env('MODULE_MEDIA', true), FILTER_VALIDATE_BOOLEAN),
    'contact'        => filter_var(env('MODULE_CONTACT', true), FILTER_VALIDATE_BOOLEAN),
    'email_templates' => filter_var(env('MODULE_EMAIL_TEMPLATES', true), FILTER_VALIDATE_BOOLEAN),
    'activity_logs'  => filter_var(env('MODULE_ACTIVITY_LOGS', true), FILTER_VALIDATE_BOOLEAN),
    'translations'   => filter_var(env('MODULE_TRANSLATIONS', true), FILTER_VALIDATE_BOOLEAN),

    'catalog'     => filter_var(env('MODULE_CATALOG', false), FILTER_VALIDATE_BOOLEAN),
    'booking'     => filter_var(env('MODULE_BOOKING', false), FILTER_VALIDATE_BOOLEAN),
    'blog'        => filter_var(env('MODULE_BLOG', false), FILTER_VALIDATE_BOOLEAN),
    'client_auth' => filter_var(env('MODULE_CLIENT_AUTH', false), FILTER_VALIDATE_BOOLEAN),
];
