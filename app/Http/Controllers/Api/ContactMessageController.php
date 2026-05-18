<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminContactMessageRequest;
use App\Http\Requests\Api\ContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use App\Http\Resources\ContactMessageCollection;
use App\Models\ContactMessage;
use App\Support\ToggleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    use ToggleStatus;
    public function index(Request $request)
    {
        $this->authorize('viewAny', ContactMessage::class);

        $query = ContactMessage::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%"))
            ->when($request->is_read !== null, fn($q) => $q->where('is_read', $request->is_read))
            ->when($request->from_date, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->to_date, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return new ContactMessageCollection($query);
    }

    public function store(ContactMessageRequest $request)
    {
        $validated = $request->validated();

        $contactMessage = ContactMessage::create($validated);

        // Log activity
        activity_log('contact_message.created', [
            'contact_message_id' => $contactMessage->id,
        ]);

        return new ContactMessageResource($contactMessage);
    }

    public function show(ContactMessage $contactMessage)
    {
        $this->authorize('view', $contactMessage);

        return new ContactMessageResource($contactMessage);
    }

    public function update(AdminContactMessageRequest $request, ContactMessage $contactMessage)
    {
        $this->authorize('update', $contactMessage);

        $validated = $request->validated();

        $contactMessage->update($validated);

        // Log activity if marked as read or replied
        if ($request->has('is_read') && $request->is_read) {
            activity_log('contact_message.read', [
                'contact_message_id' => $contactMessage->id,
            ]);
        }

        if ($request->has('reply')) {
            activity_log('contact_message.replied', [
                'contact_message_id' => $contactMessage->id,
            ]);
        }

        return new ContactMessageResource($contactMessage);
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $this->authorize('delete', $contactMessage);

        $contactMessage->delete();

        // Log activity
        activity_log('contact_message.deleted', [
            'contact_message_id' => $contactMessage->id,
        ]);

        return response()->json(['message' => 'Contact message deleted successfully']);
    }

    public function toggleStatus(ContactMessage $contactMessage): JsonResponse
    {
        return $this->doToggleStatus($contactMessage);
    }
}