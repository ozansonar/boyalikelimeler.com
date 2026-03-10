<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class MailTemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject'   => 'required|string|max:300',
            'body'      => 'required|string|max:65000',
            'is_active' => 'required|in:0,1',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Konu alanı zorunludur.',
            'subject.max'      => 'Konu en fazla 300 karakter olabilir.',
            'body.required'    => 'Mail gövdesi zorunludur.',
            'body.max'         => 'Mail gövdesi çok uzun.',
            'is_active.required' => 'Durum alanı zorunludur.',
            'is_active.in'       => 'Geçersiz durum değeri.',
        ];
    }
}
