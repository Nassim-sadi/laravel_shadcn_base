<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Http\Resources\ContactMessageCollection;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%"))
            ->when($request->is_read !== null, fn($q) => $q->where('is_read', $request->is_read))
            ->when($request->from_date, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->to_date, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return new ContactMessageCollection($query);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contactMessage = ContactMessage::create($validated);

        // Log activity
        activity_log('contact_message.created', [
            'contact_message_id' => $contactMessage->id,
        ]);

        return new ContactMessageResource($contactMessage);
    }

    public function show(ContactMessage $contactMessage)
    {
        return new ContactMessageResource($contactMessage);
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'subject' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'is_read' => 'sometimes|boolean',
            'reply' => 'sometimes|string',
            'replied_at' => 'sometimes|date',
        ]);

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
        $contactMessage->delete();

        // Log activity
        activity_log('contact_message.deleted', [
            'contact_message_id' => $contactMessage->id,
        ]);

        return response()->json(['message' => 'Contact message deleted successfully']);
    }
}