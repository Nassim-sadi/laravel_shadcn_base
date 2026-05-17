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
        
        // Set user from context (captured in Logger before queuing)
        if (isset($this->context['user_id'])) {
            $activityLog->user_id = $this->context['user_id'];
        }
        
        // Try to infer subject if not explicitly provided
        $subjectType = $this->context['subject_type'] ?? null;
        $subjectId = $this->context['subject_id'] ?? null;
        
        if (!$subjectType && !$subjectId) {
            $parts = explode('.', $this->event);
            if (count($parts) >= 2) {
                $modelName = $parts[0]; // e.g. 'media', 'service', 'user'
                $idKey = $modelName . '_id';
                if (isset($this->context[$idKey])) {
                    $subjectId = $this->context[$idKey];
                    // Convert snake_case model name to StudlyCase class name
                    $className = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $modelName)));
                    $subjectType = 'App\\Models\\' . $className;
                }
            }
        }
        
        $activityLog->subject_type = $subjectType;
        $activityLog->subject_id = $subjectId;
        
        // Set description, generate fallback if needed
        $description = $this->context['description'] ?? null;
        if (!$description) {
            $parts = explode('.', $this->event);
            $action = count($parts) > 1 ? ucfirst($parts[1]) : 'Performed';
            $model = count($parts) > 1 ? ucfirst(str_replace('_', ' ', $parts[0])) : $this->event;
            $description = "{$action} {$model}";
        }
        $activityLog->description = $description;
        
        // Set properties (excluding reserved keys and dynamically inferred subject keys)
        $reservedKeys = ['user_id', 'subject_type', 'subject_id', 'description', 'ip_address', 'user_agent'];
        if ($subjectId && isset($idKey)) {
            $reservedKeys[] = $idKey;
        }
        
        $properties = array_diff_key($this->context, array_flip($reservedKeys));
        $activityLog->properties = !empty($properties) ? $properties : null;
        
        // Set IP address and user agent if provided
        $activityLog->ip_address = $this->context['ip_address'] ?? null;
        $activityLog->user_agent = $this->context['user_agent'] ?? null;
        
        $activityLog->save();
    }
}