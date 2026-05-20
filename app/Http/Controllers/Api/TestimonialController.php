<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\TestimonialCollection;
use App\Models\Testimonial;
use App\Support\ToggleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    use ToggleStatus;
    public function index(Request $request)
    {
        $this->authorize('viewAny', Testimonial::class);

        $testimonials = Testimonial::query()
            ->with('image')
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('company', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->rating, fn($q, $rating) => $q->where('rating', $rating))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new TestimonialCollection($testimonials);
    }

    public function store(TestimonialRequest $request)
    {
        $this->authorize('create', Testimonial::class);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'testimonial_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('testimonials', $filename, 'public');

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

        $testimonial = Testimonial::create($validated);

        activity_log('testimonial.created', [
            'testimonial_id' => $testimonial->id,
            'user_id' => auth()->id(),
        ]);

        return new TestimonialResource($testimonial->load('image'));
    }

    public function show(Testimonial $testimonial)
    {
        $this->authorize('view', $testimonial);

        return new TestimonialResource($testimonial->load('image'));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $this->authorize('update', $testimonial);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($testimonial->image_id) {
                $oldMedia = \App\Models\Media::find($testimonial->image_id);
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
            $filename = 'testimonial_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('testimonials', $filename, 'public');

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

        $testimonial->update($validated);

        activity_log('testimonial.updated', [
            'testimonial_id' => $testimonial->id,
            'user_id' => auth()->id(),
        ]);

        return new TestimonialResource($testimonial->load('image'));
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->authorize('delete', $testimonial);

        if ($testimonial->image_id) {
            $media = \App\Models\Media::find($testimonial->image_id);
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

        $testimonial->delete();

        activity_log('testimonial.deleted', [
            'testimonial_id' => $testimonial->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Testimonial deleted successfully']);
    }

    public function toggleStatus(Testimonial $testimonial): JsonResponse
    {
        return $this->doToggleStatus($testimonial);
    }
}