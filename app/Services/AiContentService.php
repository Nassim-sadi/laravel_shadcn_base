<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiContentService
{
    public function __construct(
        private readonly AiModuleRegistry $moduleRegistry,
        private readonly AiSettingsService $settingsService,
    ) {
    }

    public function generateContent(array $payload): array
    {
        $config = $this->settingsService->getRuntimeConfig();
        $provider = $config['provider'];
        $apiKey = $config['api_key'];
        $model = $config['model'];
        $baseUrl = $config['base_url'];
        $timeout = $config['timeout'];

        if ($provider !== 'openai') {
            throw new RuntimeException("Unsupported AI provider [{$provider}].");
        }

        if ($apiKey === '') {
            throw new RuntimeException('AI generation is not configured yet. Add an API key in AI settings or AI_API_KEY.');
        }

        $module = (string) $payload['module'];
        $fields = array_values($payload['fields']);
        $locale = $payload['locale'];
        $mode = $payload['mode'];
        $tone = trim((string) ($payload['tone'] ?? ''));
        $context = trim((string) ($payload['context'] ?? ''));
        $source = Arr::only($payload['source'] ?? [], $fields);

        $response = Http::baseUrl($baseUrl)
            ->timeout($timeout)
            ->withToken($apiKey)
            ->acceptJson()
            ->post('/responses', [
                'model' => $model,
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->buildSystemPrompt($module, $fields),
                            ],
                        ],
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->buildUserPrompt($module, $fields, $locale, $mode, $tone, $context, $source),
                            ],
                        ],
                    ],
                ],
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => "{$module}_content_generation",
                        'schema' => $this->responseSchema($fields),
                        'strict' => true,
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('AI generation failed. Please try again.');
        }

        $content = data_get($response->json(), 'output.0.content.0.text');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('AI generation returned an empty response.');
        }

        $decoded = json_decode($content, true);

        if (! is_array($decoded) || ! isset($decoded['fields']) || ! is_array($decoded['fields'])) {
            throw new RuntimeException('AI generation returned an invalid response.');
        }

        return [
            'fields' => Arr::only($decoded['fields'], $fields),
            'usage' => [
                'input_tokens' => data_get($response->json(), 'usage.input_tokens'),
                'output_tokens' => data_get($response->json(), 'usage.output_tokens'),
                'total_tokens' => data_get($response->json(), 'usage.total_tokens'),
                'model' => $model,
            ],
        ];
    }

    private function buildSystemPrompt(string $module, array $fields): string
    {
        $moduleLabel = $this->moduleRegistry->labelFor($module);

        $lines = [
            "You are an assistant that drafts admin-reviewed marketing content for {$moduleLabel}.",
            'Return only valid JSON matching the provided schema.',
            'Do not include markdown fences or commentary.',
            'Write concise, useful copy for the requested locale.',
            'If mode is improve, preserve the original meaning while making the text clearer and more polished.',
        ];

        if (in_array('seo_keywords', $fields, true)) {
            $lines[] = 'For seo_keywords, return a short comma-separated string of keywords.';
        }

        if ($module === AiModuleRegistry::MODULE_BLOG_POSTS && in_array('body', $fields, true)) {
            $lines[] = 'For blog body content, return simple HTML with paragraphs and headings instead of markdown.';
        }

        return implode("\n", $lines);
    }

    private function buildUserPrompt(
        string $module,
        array $fields,
        string $locale,
        string $mode,
        string $tone,
        string $context,
        array $source,
    ): string {
        $parts = [
            "Module: {$module}",
            "Locale: {$locale}",
            "Mode: {$mode}",
            'Fields: ' . implode(', ', $fields),
        ];

        if ($tone !== '') {
            $parts[] = "Tone: {$tone}";
        }

        if ($context !== '') {
            $parts[] = "Additional context:\n{$context}";
        }

        if ($source !== []) {
            $parts[] = 'Existing content to use when relevant:';
            foreach ($source as $field => $value) {
                if (is_string($value) && trim($value) !== '') {
                    $parts[] = "- {$field}: {$value}";
                }
            }
        }

        $parts[] = 'Return generated text for every requested field. Avoid placeholders unless the source itself is missing critical information.';

        return implode("\n\n", $parts);
    }

    private function responseSchema(array $fields): array
    {
        $fieldProperties = [];
        foreach ($fields as $field) {
            $fieldProperties[$field] = [
                'type' => 'string',
            ];
        }

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['fields'],
            'properties' => [
                'fields' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => array_values($fields),
                    'properties' => $fieldProperties,
                ],
            ],
        ];
    }
}
