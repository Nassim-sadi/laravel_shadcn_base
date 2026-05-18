<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EmailTemplateRequest;
use App\Http\Resources\EmailTemplateResource;
use App\Http\Resources\EmailTemplateCollection;
use App\Models\EmailTemplate;
use App\Support\ToggleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    use ToggleStatus;
    public function index(Request $request)
    {
        $this->authorize('viewAny', EmailTemplate::class);

        $query = EmailTemplate::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('key', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return new EmailTemplateCollection($query);
    }

    public function store(EmailTemplateRequest $request)
    {
        $this->authorize('create', EmailTemplate::class);

        $validated = $request->validated();

        $emailTemplate = EmailTemplate::create($validated);

        activity_log('email_template.created', [
            'email_template_id' => $emailTemplate->id,
            'user_id' => auth()->id(),
        ]);

        return new EmailTemplateResource($emailTemplate);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        $this->authorize('view', $emailTemplate);

        return new EmailTemplateResource($emailTemplate);
    }

    public function update(EmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        $this->authorize('update', $emailTemplate);

        $validated = $request->validated();

        $emailTemplate->update($validated);

        activity_log('email_template.updated', [
            'email_template_id' => $emailTemplate->id,
            'user_id' => auth()->id(),
        ]);

        return new EmailTemplateResource($emailTemplate);
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $this->authorize('delete', $emailTemplate);

        $emailTemplate->delete();

        activity_log('email_template.deleted', [
            'email_template_id' => $emailTemplate->id,
            'user_id' => auth()->id(),
        ]);

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

    public function toggleStatus(EmailTemplate $emailTemplate): JsonResponse
    {
        return $this->doToggleStatus($emailTemplate);
    }
}