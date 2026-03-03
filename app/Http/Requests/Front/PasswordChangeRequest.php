<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class PasswordChangeRequest extends FormRequest
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
            'old_password' => ['required', 'string'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'old_password.required' => 'Mevcut şifrenizi giriniz.',
            'password.required'     => 'Yeni şifre alanı zorunludur.',
            'password.min'          => 'Yeni şifre en az 8 karakter olmalıdır.',
            'password.confirmed'    => 'Şifreler eşleşmiyor.',
        ];
    }
}
