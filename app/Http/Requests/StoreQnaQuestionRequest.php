<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQnaQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'min:10', 'max:255'],
            'body'            => ['required', 'string', 'min:20', 'max:5000'],
            'qna_category_id' => ['required', 'integer', 'exists:qna_categories,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'           => 'Soru başlığı zorunludur.',
            'title.min'                => 'Soru başlığı en az 10 karakter olmalıdır.',
            'title.max'                => 'Soru başlığı en fazla 255 karakter olabilir.',
            'body.required'            => 'Soru detayı zorunludur.',
            'body.min'                 => 'Soru detayı en az 20 karakter olmalıdır.',
            'body.max'                 => 'Soru detayı en fazla 5000 karakter olabilir.',
            'qna_category_id.required' => 'Kategori seçimi zorunludur.',
            'qna_category_id.exists'   => 'Geçersiz kategori seçimi.',
        ];
    }
}
