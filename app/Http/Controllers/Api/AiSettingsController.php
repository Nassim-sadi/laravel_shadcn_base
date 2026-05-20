<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AiSettingsRequest;
use App\Models\Setting;
use App\Services\AiSettingsService;

class AiSettingsController extends Controller
{
    public function __construct(
        private readonly AiSettingsService $settingsService,
    ) {
    }

    public function show()
    {
        $this->authorize('viewAny', Setting::class);

        return response()->json([
            'message' => 'AI settings loaded successfully.',
            'data' => $this->settingsService->getEditableSettings(),
        ]);
    }

    public function update(AiSettingsRequest $request)
    {
        $settings = $this->settingsService->saveEditableSettings($request->validated());

        activity_log('settings.ai.updated', [
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'AI settings saved successfully.',
            'data' => $settings,
        ]);
    }
}
