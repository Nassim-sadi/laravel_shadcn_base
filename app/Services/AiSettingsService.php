<?php

namespace App\Services;

use App\Models\Setting;

class AiSettingsService
{
    private const SETTINGS = [
        'provider' => ['key' => 'ai_provider', 'type' => 'string', 'default' => 'openai', 'name' => 'AI Provider'],
        'api_key' => ['key' => 'ai_api_key', 'type' => 'encrypted', 'default' => null, 'name' => 'AI API Key'],
        'model' => ['key' => 'ai_model', 'type' => 'string', 'default' => 'gpt-4.1-mini', 'name' => 'AI Model'],
        'base_url' => ['key' => 'ai_base_url', 'type' => 'string', 'default' => 'https://api.openai.com/v1', 'name' => 'AI Base URL'],
        'timeout' => ['key' => 'ai_timeout', 'type' => 'integer', 'default' => 30, 'name' => 'AI Timeout'],
    ];

    public function getRuntimeConfig(): array
    {
        return [
            'provider' => (string) ($this->getSettingValue('provider') ?: config('services.ai.provider', 'openai')),
            'api_key' => (string) ($this->getSettingValue('api_key') ?: config('services.ai.api_key', '')),
            'model' => (string) ($this->getSettingValue('model') ?: config('services.ai.model', 'gpt-4.1-mini')),
            'base_url' => rtrim((string) ($this->getSettingValue('base_url') ?: config('services.ai.base_url', 'https://api.openai.com/v1')), '/'),
            'timeout' => (int) ($this->getSettingValue('timeout') ?: config('services.ai.timeout', 30)),
        ];
    }

    public function getEditableSettings(): array
    {
        $config = $this->getRuntimeConfig();
        $apiKey = (string) $this->getSettingValue('api_key');

        return [
            'provider' => $config['provider'],
            'model' => $config['model'],
            'base_url' => $config['base_url'],
            'timeout' => $config['timeout'],
            'has_api_key' => $apiKey !== '' || (string) config('services.ai.api_key', '') !== '',
            'api_key_masked' => $apiKey !== '' ? $this->maskSecret($apiKey) : null,
        ];
    }

    public function saveEditableSettings(array $payload): array
    {
        foreach (['provider', 'model', 'base_url', 'timeout'] as $field) {
            if (! array_key_exists($field, $payload)) {
                continue;
            }

            $this->upsertSetting($field, $payload[$field]);
        }

        if (array_key_exists('api_key', $payload) && is_string($payload['api_key']) && trim($payload['api_key']) !== '') {
            $this->upsertSetting('api_key', trim($payload['api_key']));
        }

        return $this->getEditableSettings();
    }

    public function seedDefaults(): void
    {
        foreach (self::SETTINGS as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => 'ai',
                    'name' => $setting['name'],
                    'type' => $setting['type'],
                    'value' => $setting['default'],
                    'default_value' => $setting['default'],
                    'description' => null,
                    'is_public' => false,
                ],
            );
        }
    }

    private function getSettingValue(string $field): mixed
    {
        $settingKey = self::SETTINGS[$field]['key'];

        return Setting::get($settingKey, self::SETTINGS[$field]['default']);
    }

    private function upsertSetting(string $field, mixed $value): void
    {
        $definition = self::SETTINGS[$field];

        Setting::updateOrCreate(
            ['key' => $definition['key']],
            [
                'group' => 'ai',
                'name' => $definition['name'],
                'type' => $definition['type'],
                'value' => $value,
                'default_value' => $definition['default'],
                'description' => null,
                'is_public' => false,
            ],
        );
    }

    private function maskSecret(string $secret): string
    {
        if ($secret === '') {
            return '';
        }

        $visible = substr($secret, -4);

        return str_repeat('*', max(strlen($secret) - 4, 8)) . $visible;
    }
}
