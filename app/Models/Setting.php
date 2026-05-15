<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'value',
        'default_value',
        'type',
        'name',
        'description',
        'is_public',
    ];

    protected $casts = [
        'value' => 'string',
        'default_value' => 'string',
        'is_public' => 'boolean',
    ];

    /**
     * Get the value attribute, casting it based on the type.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $this->castValue($value, $attributes['type'] ?? 'string'),
            set: fn($value, $attributes) => $this->castValueForStorage($value, $attributes['type'] ?? 'string')
        );
    }

    /**
     * Cast the value based on the type.
     */
    protected function castValue($value, $type): mixed
    {
        if ($value === null) {
            return null;
        }

        match ($type) {
            'integer' => intval($value),
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Cast the value for storage.
     */
    protected function castValueForStorage($value, $type): string
    {
        if ($value === null) {
            return null;
        }

        match ($type) {
            'integer', 'boolean' => strval($value),
            'json', 'array' => json_encode($value),
            default => $value,
        };
    }

    /**
     * Get the setting value with fallback to default.
     */
    public function getValue(): mixed
    {
        return $this->value ?? $this->default_value;
    }

    /**
     * Check if the setting is of a specific type.
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include settings in a specific group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get a setting by key.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Set a setting by key.
     */
    public static function set(string $key, $value, array $additional = []): self
    {
        $setting = static::firstOrCreate(
            ['key' => $key],
            array_merge([
                'value' => $value,
            ], $additional)
        );

        if (!$setting->wasRecentlyCreated) {
            $setting->update(['value' => $value]);
        }

        return $setting;
    }
}