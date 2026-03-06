<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminPasswordUpdateRequest extends FormRequest
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
            'current_password' => 'required|string|current_password',
            'password'         => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#._\-]/',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required'         => 'Mevcut şifrenizi girin.',
            'current_password.current_password'  => 'Mevcut şifreniz doğru değil.',
            'password.required'                  => 'Yeni şifre alanı zorunludur.',
            'password.min'                       => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed'                 => 'Şifre tekrarı eşleşmiyor.',
            'password.regex'                     => 'Şifre en az bir küçük harf, bir büyük harf, bir rakam ve bir özel karakter içermelidir.',
        ];
    }
}
