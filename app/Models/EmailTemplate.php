<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasTranslatedAttributes;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'subject' => 'array',
        'body' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the variables attribute as an array.
     */
    protected function variables(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_array($value) ? $value : json_decode($value, true) ?? [],
            set: fn($value) => is_array($value) ? json_encode($value) : $value
        );
    }

    /**
     * Get the template variables used in subject and body.
     */
    public function getUsedVariables(): array
    {
        $pattern = '/\{([^}]+)\}/';
        preg_match_all($pattern, $this->subject . $this->body, $matches);
        
        return array_unique($matches[1] ?? []);
    }

    /**
     * Render the template with provided data.
     */
    public function render(array $data = [], ?string $locale = null): array
    {
        $subject = $this->translated('subject', $locale);
        $body = $this->translated('body', $locale);
        
        foreach ($data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }
        
        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}