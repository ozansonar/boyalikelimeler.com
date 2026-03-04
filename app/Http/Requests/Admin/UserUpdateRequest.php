<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'        => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id'         => ['required', 'exists:roles,id'],
            'email_verified'       => ['nullable', 'boolean'],
            'golden_pen_periods'              => ['nullable', 'array'],
            'golden_pen_periods.*.starts_at'  => ['required', 'date'],
            'golden_pen_periods.*.ends_at'    => ['required', 'date', 'after_or_equal:golden_pen_periods.*.starts_at'],
            'golden_pen_periods.*.note'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.max'      => 'Ad en fazla 100 karakter olabilir.',
            'last_name.required'  => 'Soyad alanı zorunludur.',
            'last_name.max'       => 'Soyad en fazla 100 karakter olabilir.',
            'email.required'      => 'E-posta adresi zorunludur.',
            'email.email'         => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique'        => 'Bu e-posta adresi zaten kayıtlı.',
            'password.min'        => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed'  => 'Şifreler eşleşmiyor.',
            'role_id.required'                         => 'Rol seçimi zorunludur.',
            'role_id.exists'                           => 'Geçersiz rol.',
            'golden_pen_periods.*.starts_at.required'  => 'Dönem başlangıç tarihi zorunludur.',
            'golden_pen_periods.*.starts_at.date'      => 'Geçerli bir başlangıç tarihi giriniz.',
            'golden_pen_periods.*.ends_at.required'    => 'Dönem bitiş tarihi zorunludur.',
            'golden_pen_periods.*.ends_at.date'        => 'Geçerli bir bitiş tarihi giriniz.',
            'golden_pen_periods.*.ends_at.after_or_equal' => 'Bitiş tarihi başlangıç tarihinden önce olamaz.',
        ];
    }
}
