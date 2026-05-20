<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AiContentImportConfirmRequest;
use App\Http\Requests\Api\AiContentImportPreviewRequest;
use App\Services\AiContentImportService;
use RuntimeException;

class AiImportController extends Controller
{
    public function __construct(
        private readonly AiContentImportService $importService,
    ) {
    }

    public function preview(AiContentImportPreviewRequest $request)
    {
        try {
            $preview = $this->importService->preview(
                $request->string('module')->toString(),
                $request->file('file'),
                $request->user(),
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $status = $preview['valid'] ? 200 : 422;

        return response()->json([
            'message' => $preview['message'],
            'data' => $preview,
        ], $status);
    }

    public function confirm(AiContentImportConfirmRequest $request)
    {
        try {
            $result = $this->importService->confirm(
                $request->string('preview_token')->toString(),
                $request->user(),
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Content imported successfully.',
            'data' => $result,
        ]);
    }
}
