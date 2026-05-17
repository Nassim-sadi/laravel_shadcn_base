<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\ActivityLogCollection;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $activityLogs = ActivityLog::query()
            ->when($request->search, fn($q, $search) => $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                }))
            ->when($request->event, fn($q, $event) => $q->where('event', $event))
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->with('user')
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);
   
        return new ActivityLogCollection($activityLogs);
    }

    public function show(ActivityLog $activityLog)
    {
        $this->authorize('view', $activityLog);

        $activityLog->load('user');
        return new ActivityLogResource($activityLog);
    }

    public function getLogNames()
    {
        // For compatibility with existing API, we'll use event as log_name equivalent
        $logNames = ActivityLog::select('event')
            ->distinct()
            ->pluck('event');

        return response()->json($logNames);
    }

    public function getEvents()
    {
        $events = ActivityLog::select('event')
            ->distinct()
            ->whereNotNull('event')
            ->pluck('event');

        return response()->json($events);
    }
}