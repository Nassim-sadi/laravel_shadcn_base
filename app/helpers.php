<?php

if (!function_exists('activity_log')) {
    function activity_log(string $event, array $context = []): void
    {
        \App\Support\Activity\ActivityLogger::log($event, $context);
    }
}

if (!function_exists('setting')) {
    function setting(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return \App\Models\Setting::class;
        }

        return \App\Models\Setting::get($key, $default);
    }
}