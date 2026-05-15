<?php

if (!function_exists('activity_log')) {
    /**
     * Log an activity.
     *
     * @param string $event
     * @param array  $context
     * @return void
     */
    function activity_log(string $event, array $context = []): void
    {
        \App\Support\Activity\ActivityLogger::log($event, $context);
    }
}