<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                => ['required', 'email'],
            'password'             => ['required', 'string'],
            'g-recaptcha-response' => ['sometimes', new RecaptchaRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'E-posta adresi zorunludur.',
            'email.email'       => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre zorunludur.',
        ];
    }
}
