<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
        $userId = $this->user()->id;

        return [
            'name'           => ['required', 'string', 'max:100'],
            'username'       => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users')->ignore($userId)],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'bio'            => ['nullable', 'string', 'max:300'],
            'about'          => ['nullable', 'string', 'max:5000'],
            'location'       => ['nullable', 'string', 'max:100'],
            'website'        => ['nullable', 'url', 'max:255'],
            'birthdate'      => ['nullable', 'date', 'before:today'],
            'gender'         => ['nullable', 'in:female,male,other'],
            'instagram'      => ['nullable', 'string', 'max:50'],
            'twitter'        => ['nullable', 'string', 'max:50'],
            'youtube'        => ['nullable', 'string', 'max:50'],
            'tiktok'         => ['nullable', 'string', 'max:50'],
            'spotify'        => ['nullable', 'string', 'max:100'],
            'interests'      => ['nullable', 'array'],
            'interests.*'    => ['string', 'max:50'],
            'is_public'      => ['boolean'],
            'show_email'     => ['boolean'],
            'show_last_seen' => ['boolean'],
            'allow_messages' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Ad alanı zorunludur.',
            'username.required'  => 'Kullanıcı adı zorunludur.',
            'username.min'       => 'Kullanıcı adı en az 3 karakter olmalıdır.',
            'username.max'       => 'Kullanıcı adı en fazla 30 karakter olabilir.',
            'username.regex'     => 'Kullanıcı adı yalnızca harf, rakam ve alt çizgi içerebilir.',
            'username.unique'    => 'Bu kullanıcı adı zaten kullanılıyor.',
            'email.required'     => 'E-posta alanı zorunludur.',
            'email.email'        => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique'       => 'Bu e-posta adresi zaten kullanılıyor.',
            'bio.max'            => 'Biyografi en fazla 300 karakter olabilir.',
            'website.url'        => 'Geçerli bir URL giriniz.',
            'birthdate.before'   => 'Doğum tarihi bugünden önce olmalıdır.',
        ];
    }
}
