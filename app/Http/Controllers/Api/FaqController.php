<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FaqRequest;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FaqCollection;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Faq::class);

        $faqs = Faq::query()
            ->when($request->search, fn($q, $search) => $q->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new FaqCollection($faqs);
    }

    public function store(FaqRequest $request)
    {
        $this->authorize('create', Faq::class);

        $validated = $request->validated();

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
        $this->authorize('view', $faq);

        return new FaqResource($faq);
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $this->authorize('update', $faq);

        $validated = $request->validated();

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
        $this->authorize('delete', $faq);

        $faq->delete();

        // Log activity
        activity_log('faq.deleted', [
            'faq_id' => $faq->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'FAQ deleted successfully']);
    }
}