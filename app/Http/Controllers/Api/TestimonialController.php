<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\TestimonialCollection;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $testimonials = Testimonial::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('company', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->rating, fn($q, $rating) => $q->where('rating', $rating))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new TestimonialCollection($testimonials);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'rating' => 'sometimes|integer|min:1|max:5',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

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

        return new TestimonialResource($testimonial);
    }

    public function show(Testimonial $testimonial)
    {
        return new TestimonialResource($testimonial);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'company' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'rating' => 'sometimes|integer|min:1|max:5',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

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

        return new TestimonialResource($testimonial);
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