<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('description', 'like', "%{$search}%")
                        ->orWhereHas('causer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->log_name, fn($q, $log_name) => $q->where('log_name', $log_name))
            ->when($request->event, fn($q, $event) => $q->where('event', $event))
            ->when($request->causer_id, fn($q, $causer_id) => $q->where('causer_id', $causer_id))
            ->with('causer')
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($activities);
    }

    public function show(Activity $activity)
    {
        $activity->load('causer', 'subject');
        return response()->json($activity);
    }

    public function getLogNames()
    {
        $logNames = Activity::select('log_name')
            ->distinct()
            ->pluck('log_name');

        return response()->json($logNames);
    }

    public function getEvents()
    {
        $events = Activity::select('event')
            ->distinct()
            ->whereNotNull('event')
            ->pluck('event');

        return response()->json($events);
    }
}