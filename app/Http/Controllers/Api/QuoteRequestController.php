<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreQuoteRequestRequest;
use App\Http\Resources\QuoteRequestResource;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', QuoteRequest::class);

        $quotes = QuoteRequest::query()
            ->with('product')
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->when($request->is_read !== null, fn($q) => $q->where('is_read', $request->is_read))
            ->when($request->product_id, fn($q, $id) => $q->where('product_id', $id))
            ->recentFirst()
            ->paginate($request->per_page ?? 15);

        return QuoteRequestResource::collection($quotes);
    }

    public function store(StoreQuoteRequestRequest $request)
    {
        $validated = $request->validated();

        $quote = QuoteRequest::create($validated);

        activity_log('quote_request.created', [
            'quote_id' => $quote->id,
            'product_id' => $quote->product_id,
        ]);

        return new QuoteRequestResource($quote);
    }

    public function show(QuoteRequest $quoteRequest)
    {
        $this->authorize('view', $quoteRequest);

        if (! $quoteRequest->is_read) {
            $quoteRequest->update(['is_read' => true]);
        }

        return new QuoteRequestResource($quoteRequest->load('product'));
    }

    public function reply(Request $request, QuoteRequest $quoteRequest)
    {
        $this->authorize('update', $quoteRequest);

        $validated = $request->validate([
            'reply' => 'required|string',
        ]);

        $quoteRequest->update([
            'reply' => $validated['reply'],
            'replied_at' => now(),
        ]);

        activity_log('quote_request.replied', [
            'quote_id' => $quoteRequest->id,
        ]);

        return new QuoteRequestResource($quoteRequest);
    }

    public function destroy(QuoteRequest $quoteRequest)
    {
        $this->authorize('delete', $quoteRequest);

        $quoteRequest->delete();

        activity_log('quote_request.deleted', [
            'quote_id' => $quoteRequest->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Quote request deleted successfully']);
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', QuoteRequest::class);

        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:quote_requests,id'],
        ]);

        $count = QuoteRequest::whereIn('id', $request->ids)->count();
        QuoteRequest::whereIn('id', $request->ids)->delete();

        activity_log('quote_request.bulk_deleted', [
            'count' => $count,
        ]);

        return response()->json([
            'message' => 'Deleted successfully.',
            'deleted' => $count,
        ]);
    }
}
