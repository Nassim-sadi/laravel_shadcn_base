<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmailTemplateResource;
use App\Http\Resources\EmailTemplateCollection;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('key', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return new EmailTemplateCollection($query);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:email_templates,key',
            'name' => 'required|string|max:255',
            'subject' => 'required|string',
            'body' => 'required|string',
            'variables' => 'sometimes|array',
            'variables.*' => 'string',
            'is_active' => 'sometimes|boolean',
        ]);

        $emailTemplate = EmailTemplate::create($validated);

        return new EmailTemplateResource($emailTemplate);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return new EmailTemplateResource($emailTemplate);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'key' => 'sometimes|string|max:255|unique:email_templates,key,' . $emailTemplate->id,
            'name' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string',
            'body' => 'sometimes|string',
            'variables' => 'sometimes|array',
            'variables.*' => 'string',
            'is_active' => 'sometimes|boolean',
        ]);

        $emailTemplate->update($validated);

        return new EmailTemplateResource($emailTemplate);
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return response()->json(['message' => 'Email template deleted successfully']);
    }

    /**
     * Preview the template with provided data
     */
    public function preview(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'data' => 'sometimes|array',
        ]);

        $rendered = $emailTemplate->render($validated['data'] ?? []);

        return response()->json($rendered);
    }
}