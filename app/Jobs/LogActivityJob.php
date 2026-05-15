<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $event;
    protected array $context;

    /**
     * Create a new job instance.
     */
    public function __construct(string $event, array $context = [])
    {
        $this->event = $event;
        $this->context = $context;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activityLog = new ActivityLog();
        $activityLog->event = $this->event;
        
        // Set user if authenticated
        if (Auth::check()) {
            $activityLog->user_id = Auth::id();
        }
        
        // Set subject if provided
        if (!empty($this->context['subject_type']) && !empty($this->context['subject_id'])) {
            $activityLog->subject_type = $this->context['subject_type'];
            $activityLog->subject_id = $this->context['subject_id'];
        }
        
        // Set description
        $activityLog->description = $this->context['description'] ?? null;
        
        // Set properties (excluding reserved keys)
        $reservedKeys = ['subject_type', 'subject_id', 'description', 'ip_address', 'user_agent'];
        $properties = array_diff_key($this->context, array_flip($reservedKeys));
        $activityLog->properties = !empty($properties) ? $properties : null;
        
        // Set IP address and user agent if provided
        $activityLog->ip_address = $this->context['ip_address'] ?? null;
        $activityLog->user_agent = $this->context['user_agent'] ?? null;
        
        $activityLog->save();
    }
}