<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AiContentGenerateRequest;
use App\Services\AiContentService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class AiContentController extends Controller
{
    public function __construct(
        private readonly AiContentService $aiContentService,
    ) {
    }

    public function generate(AiContentGenerateRequest $request): JsonResponse
    {
        try {
            $generated = $this->aiContentService->generateContent($request->validated());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Content generated successfully.',
            'data' => $generated,
        ]);
    }
}
