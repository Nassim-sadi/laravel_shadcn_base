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
        // Capture context if not explicitly provided
        if (!isset($context['user_id']) && \Illuminate\Support\Facades\Auth::check()) {
            $context['user_id'] = \Illuminate\Support\Facades\Auth::id();
        }

        if (!isset($context['ip_address']) && request()) {
            $context['ip_address'] = request()->ip();
        }

        if (!isset($context['user_agent']) && request()) {
            $context['user_agent'] = request()->userAgent();
        }

        // Dispatch the job to the queue to avoid slowing down the request
        LogActivityJob::dispatch($event, $context);
    }
}