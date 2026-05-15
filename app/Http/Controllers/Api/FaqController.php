<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FaqCollection;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::query()
            ->when($request->search, fn($q, $search) => $q->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new FaqCollection($faqs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:255',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $faq = Faq::create($validated);

        // Log activity
        activity_log('faq.created', [
            'faq_id' => $faq->id,
            'user_id' => auth()->id(),
        ]);

        return new FaqResource($faq);
    }

    public function show(Faq $faq)
    {
        return new FaqResource($faq);
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'sometimes|string|max:255',
            'answer' => 'sometimes|string',
            'category' => 'sometimes|string|max:255',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $faq->update($validated);

        // Log activity
        activity_log('faq.updated', [
            'faq_id' => $faq->id,
            'user_id' => auth()->id(),
        ]);

        return new FaqResource($faq);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        // Log activity
        activity_log('faq.deleted', [
            'faq_id' => $faq->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'FAQ deleted successfully']);
    }
}