<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

class AiContentImportService
{
    public function __construct(
        private readonly AiModuleRegistry $moduleRegistry,
    ) {
    }

    public function preview(string $module, UploadedFile $file, User $user): array
    {
        $payload = $this->decodeFile($file);
        $items = Arr::wrap($payload);

        if ($items === [] || ! array_is_list($payload)) {
            return [
                'valid' => false,
                'message' => 'The JSON file must contain a top-level array of items.',
                'item_count' => is_array($payload) ? count($payload) : 0,
                'row_errors' => [],
            ];
        }

        $validatedItems = [];
        $rowErrors = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                $rowErrors[] = [
                    'row' => $index + 1,
                    'errors' => ['row' => ['Each row must be a JSON object.']],
                ];
                continue;
            }

            $result = $this->moduleRegistry->validateImportItem($module, $item);

            if (! $result['valid']) {
                $rowErrors[] = [
                    'row' => $index + 1,
                    'errors' => $result['errors'],
                ];
                continue;
            }

            $validatedItems[] = $result['validated'];
        }

        if ($rowErrors !== []) {
            return [
                'valid' => false,
                'message' => 'Import validation failed. Fix the highlighted rows and try again.',
                'item_count' => count($items),
                'row_errors' => $rowErrors,
            ];
        }

        $previewToken = (string) Str::uuid();

        Cache::put($this->cacheKey($previewToken), [
            'module' => $module,
            'items' => $validatedItems,
            'user_id' => $user->id,
        ], now()->addMinutes(30));

        return [
            'valid' => true,
            'message' => 'Import preview is ready.',
            'preview_token' => $previewToken,
            'item_count' => count($validatedItems),
            'module' => $module,
            'module_label' => $this->moduleRegistry->labelFor($module),
            'row_errors' => [],
        ];
    }

    public function confirm(string $previewToken, User $user): array
    {
        $cached = Cache::get($this->cacheKey($previewToken));

        if (! is_array($cached)) {
            throw new RuntimeException('This import preview has expired. Please upload the JSON again.');
        }

        if (($cached['user_id'] ?? null) !== $user->id) {
            throw new RuntimeException('You are not allowed to confirm this import preview.');
        }

        $module = (string) ($cached['module'] ?? '');
        $items = $cached['items'] ?? [];

        foreach ($items as $item) {
            $this->moduleRegistry->createRecord($module, $item, $user);
        }

        Cache::forget($this->cacheKey($previewToken));

        return [
            'created_count' => count($items),
            'module' => $module,
            'module_label' => $this->moduleRegistry->labelFor($module),
        ];
    }

    private function decodeFile(UploadedFile $file): array
    {
        $contents = $file->getContent();
        $decoded = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('The uploaded file does not contain valid JSON.');
        }

        if (! is_array($decoded)) {
            throw new RuntimeException('The uploaded file must contain a JSON array.');
        }

        return $decoded;
    }

    private function cacheKey(string $previewToken): string
    {
        return "ai_content_import_preview:{$previewToken}";
    }
}
