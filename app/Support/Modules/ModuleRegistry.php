<?php

namespace App\Support\Modules;

class ModuleRegistry
{
    protected array $definitions = [
        'services' => [
            'group' => 'content',
            'label' => 'Services',
            'enabled' => true,
        ],
        'projects' => [
            'group' => 'content',
            'label' => 'Projects',
            'enabled' => true,
        ],
        'testimonials' => [
            'group' => 'content',
            'label' => 'Testimonials',
            'enabled' => true,
        ],
        'faqs' => [
            'group' => 'content',
            'label' => 'FAQs',
            'enabled' => true,
        ],
        'media' => [
            'group' => 'content',
            'label' => 'Media',
            'enabled' => true,
        ],
        'blog' => [
            'group' => 'content',
            'label' => 'Blog',
            'enabled' => false,
        ],
        'contact' => [
            'group' => 'communication',
            'label' => 'Contact',
            'enabled' => true,
        ],
        'email_templates' => [
            'group' => 'communication',
            'label' => 'Email Templates',
            'enabled' => true,
        ],
        'activity_logs' => [
            'group' => 'system',
            'label' => 'Activity Logs',
            'enabled' => true,
        ],
        'translations' => [
            'group' => 'system',
            'label' => 'Translations',
            'enabled' => true,
        ],
        'catalog' => [
            'group' => 'commerce',
            'label' => 'Catalog',
            'enabled' => false,
        ],
        'booking' => [
            'group' => 'scheduling',
            'label' => 'Booking',
            'enabled' => false,
        ],
        'client_auth' => [
            'group' => 'auth',
            'label' => 'Client Auth',
            'enabled' => false,
        ],
    ];

    protected function resolveOverrides(): array
    {
        return collect($this->definitions)
            ->mapWithKeys(fn ($def, $name) => [
                $name => filter_var(
                    env('MODULE_' . strtoupper($name), $def['enabled']),
                    FILTER_VALIDATE_BOOLEAN
                ),
            ])
            ->all();
    }

    public function resolve(): array
    {
        $overrides = $this->resolveOverrides();

        return collect($this->definitions)
            ->map(fn ($def, $name) => [
                'name' => $name,
                'enabled' => $overrides[$name] ?? $def['enabled'],
                'group' => $def['group'],
                'label' => $def['label'],
            ])
            ->values()
            ->all();
    }

    public function isEnabled(string $name): bool
    {
        $overrides = $this->resolveOverrides();
        $default = $this->definitions[$name]['enabled'] ?? false;

        return $overrides[$name] ?? $default;
    }
}
