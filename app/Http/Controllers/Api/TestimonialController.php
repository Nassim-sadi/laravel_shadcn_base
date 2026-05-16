<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\TestimonialCollection;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
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
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'testimonial_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('testimonials', $filename, 'public');
            $validated['image'] = $path;
        }

        $testimonial = Testimonial::create($validated);

        // Log activity
        activity_log('testimonial.created', [
            'testimonial_id' => $testimonial->id,
            'user_id' => auth()->id(),
        ]);

        return new TestimonialResource($testimonial->load('image'));
    }

    public function show(Testimonial $testimonial)
    {
        return new TestimonialResource($testimonial->load('image'));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
                Storage::disk('public')->delete($testimonial->image);
            }
            
            $file = $request->file('image');
            $filename = 'testimonial_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('testimonials', $filename, 'public');
            $validated['image'] = $path;
        }

        $testimonial->update($validated);

        // Log activity
        activity_log('testimonial.updated', [
            'testimonial_id' => $testimonial->id,
            'user_id' => auth()->id(),
        ]);

        return new TestimonialResource($testimonial->load('image'));
    }

    public function destroy(Testimonial $testimonial)
    {
        // Delete associated image
        if ($testimonial->image && Storage::disk('public')->exists($testimonial->image)) {
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        // Log activity
        activity_log('testimonial.deleted', [
            'testimonial_id' => $testimonial->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Testimonial deleted successfully']);
    }
}