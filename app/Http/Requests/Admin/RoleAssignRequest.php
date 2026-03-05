<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Kullanıcı seçimi zorunludur.',
            'user_id.exists'   => 'Seçilen kullanıcı bulunamadı.',
            'role_id.required' => 'Rol seçimi zorunludur.',
            'role_id.exists'   => 'Seçilen rol bulunamadı.',
        ];
    }
}
