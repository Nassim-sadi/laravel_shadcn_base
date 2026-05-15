<?php

namespace App\Support\Activity;

use App\Jobs\LogActivityJob;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $event
     * @param array  $context
     * @return void
     */
    public static function log(string $event, array $context = []): void
    {
        // Dispatch the job to the queue to avoid slowing down the request
        LogActivityJob::dispatch($event, $context);
    }
}