<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class ContactReplyRequest extends FormRequest
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
            'reply_body' => ['required', 'string', 'min:5', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reply_body.required' => 'Yanıt mesajı zorunludur.',
            'reply_body.min'      => 'Yanıt en az 5 karakter olmalıdır.',
            'reply_body.max'      => 'Yanıt en fazla 10000 karakter olabilir.',
        ];
    }
}
