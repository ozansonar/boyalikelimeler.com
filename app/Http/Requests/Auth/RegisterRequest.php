<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'           => ['required', 'string', 'max:100'],
            'last_name'            => ['required', 'string', 'max:100'],
            'username'             => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
            'email'                => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'             => ['required', 'string', 'min:8', 'confirmed'],
            'terms'                => ['accepted'],
            'g-recaptcha-response' => ['sometimes', new RecaptchaRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.max'      => 'Ad en fazla 100 karakter olabilir.',
            'last_name.required'  => 'Soyad alanı zorunludur.',
            'last_name.max'       => 'Soyad en fazla 100 karakter olabilir.',
            'username.required'   => 'Kullanıcı adı zorunludur.',
            'username.min'        => 'Kullanıcı adı en az 3 karakter olmalıdır.',
            'username.max'        => 'Kullanıcı adı en fazla 30 karakter olabilir.',
            'username.regex'      => 'Kullanıcı adı yalnızca İngilizce harf, rakam ve alt çizgi içerebilir.',
            'username.unique'     => 'Bu kullanıcı adı zaten kullanılıyor.',
            'email.required'      => 'E-posta adresi zorunludur.',
            'email.email'         => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique'        => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required'   => 'Şifre zorunludur.',
            'password.min'        => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed'  => 'Şifreler eşleşmiyor.',
            'terms.accepted'      => 'Kullanım koşullarını kabul etmelisiniz.',
        ];
    }
}
