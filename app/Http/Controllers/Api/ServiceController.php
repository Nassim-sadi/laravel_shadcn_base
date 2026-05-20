<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceCollection;
use App\Models\Service;
use App\Support\ToggleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    use ToggleStatus;
    public function index(Request $request)
    {
        $this->authorize('viewAny', Service::class);

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
        $this->authorize('create', Service::class);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'service_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');

            $media = \App\Models\Media::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'thumbnail_path' => null,
                'created_by' => auth()->id(),
            ]);

            $validated['image_id'] = $media->id;
        }

        $service = Service::create($validated);

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
        $this->authorize('update', $service);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($service->image_id) {
                $oldMedia = \App\Models\Media::find($service->image_id);
                if ($oldMedia) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldMedia->path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldMedia->path);
                    }
                    if ($oldMedia->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldMedia->thumbnail_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldMedia->thumbnail_path);
                    }
                    $oldMedia->delete();
                }
            }

            $file = $request->file('image');
            $filename = 'service_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('services', $filename, 'public');

            $media = \App\Models\Media::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'thumbnail_path' => null,
                'created_by' => auth()->id(),
            ]);

            $validated['image_id'] = $media->id;
            unset($validated['image']);
        }

        $service->update($validated);

        activity_log('service.updated', [
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return new ServiceResource($service->load('image'));
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        if ($service->image_id) {
            $media = \App\Models\Media::find($service->image_id);
            if ($media) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($media->path);
                }
                if ($media->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($media->thumbnail_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($media->thumbnail_path);
                }
                $media->delete();
            }
        }

        $service->delete();

        activity_log('service.deleted', [
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Service deleted successfully']);
    }

    public function toggleStatus(Service $service): JsonResponse
    {
        return $this->doToggleStatus($service);
    }
}
