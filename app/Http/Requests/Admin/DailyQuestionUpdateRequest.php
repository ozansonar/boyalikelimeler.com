<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DailyQuestionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string', 'max:500'],
            'published_at'  => ['required', 'date'],
            'status'        => ['required', 'in:draft,published,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'question_text.required' => 'Soru metni zorunludur.',
            'question_text.max'      => 'Soru metni en fazla 500 karakter olabilir.',
            'published_at.required'  => 'Yayın tarihi zorunludur.',
            'published_at.date'      => 'Geçerli bir tarih giriniz.',
            'status.required'        => 'Durum seçimi zorunludur.',
            'status.in'              => 'Geçersiz durum.',
        ];
    }
}
