<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceCollection;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->with('image')
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->icon, fn($q, $icon) => $q->where('icon', $icon))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new ServiceCollection($services);
    }

    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'service_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');
            $validated['image'] = $path;
        }

        $service = Service::create($validated);

        // Log activity
        activity_log('service.created', [
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return new ServiceResource($service->load('image'));
    }

    public function show(Service $service)
    {
        return new ServiceResource($service->load('image'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            
            $file = $request->file('image');
            $filename = 'service_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');
            $validated['image'] = $path;
        }

        $service->update($validated);

        // Log activity
        activity_log('service.updated', [
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return new ServiceResource($service->load('image'));
    }

    public function destroy(Service $service)
    {
        // Delete associated image
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        // Log activity
        activity_log('service.deleted', [
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
