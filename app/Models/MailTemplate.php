<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'mailable_class',
        'subject',
        'default_subject',
        'body',
        'default_body',
        'description',
        'variables',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if template has custom subject (different from default).
     */
    public function hasCustomSubject(): bool
    {
        return $this->subject !== $this->default_subject;
    }

    /**
     * Check if template has custom body (different from default).
     */
    public function hasCustomBody(): bool
    {
        return $this->body !== $this->default_body;
    }

    /**
     * Replace variables in subject with actual values.
     *
     * @param array<string, string> $replacements
     */
    public function renderSubject(array $replacements = []): string
    {
        return $this->replaceVariables($this->subject, $replacements);
    }

    /**
     * Replace variables in body with actual values.
     *
     * @param array<string, string> $replacements
     */
    public function renderBody(array $replacements = []): string
    {
        return $this->replaceVariables($this->body, $replacements);
    }

    /**
     * Replace {variable} placeholders with actual values.
     *
     * @param array<string, string> $replacements
     */
    private function replaceVariables(string $text, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $placeholder = str_starts_with($key, '{') ? $key : '{' . $key . '}';
            $text = str_replace($placeholder, $value, $text);
        }

        return $text;
    }
}
