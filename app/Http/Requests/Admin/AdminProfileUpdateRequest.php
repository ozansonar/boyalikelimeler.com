<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $socialFields = ['instagram', 'twitter', 'youtube', 'tiktok'];
        foreach ($socialFields as $field) {
            $value = $this->input($field);
            if (is_string($value)) {
                $this->merge([$field => ltrim($value, '@')]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name'      => 'required|string|max:100',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username'  => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'bio'       => 'nullable|string|max:300',
            'location'  => 'nullable|string|max:100',
            'website'   => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:50',
            'twitter'   => 'nullable|string|max:50',
            'youtube'   => 'nullable|string|max:50',
            'tiktok'    => 'nullable|string|max:50',
            'spotify'   => 'nullable|string|max:100',
            'notify_admin_mails' => 'nullable|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Ad alanı zorunludur.',
            'name.max'          => 'Ad en fazla 100 karakter olabilir.',
            'email.required'    => 'E-posta alanı zorunludur.',
            'email.email'       => 'Geçerli bir e-posta adresi girin.',
            'email.unique'      => 'Bu e-posta adresi zaten kullanılıyor.',
            'username.required' => 'Kullanıcı adı zorunludur.',
            'username.unique'   => 'Bu kullanıcı adı zaten kullanılıyor.',
            'username.regex'    => 'Kullanıcı adı sadece harf, rakam, nokta, tire ve alt çizgi içerebilir.',
            'bio.max'           => 'Biyografi en fazla 300 karakter olabilir.',
            'website.url'       => 'Geçerli bir URL girin (https://...).',
            'instagram.max'     => 'Instagram kullanıcı adı en fazla 50 karakter olabilir.',
            'twitter.max'       => 'Twitter kullanıcı adı en fazla 50 karakter olabilir.',
            'youtube.max'       => 'YouTube kanal adı en fazla 50 karakter olabilir.',
            'tiktok.max'        => 'TikTok kullanıcı adı en fazla 50 karakter olabilir.',
            'spotify.max'       => 'Spotify kullanıcı adı en fazla 100 karakter olabilir.',
        ];
    }
}
