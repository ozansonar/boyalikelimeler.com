<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuUpdateRequest extends FormRequest
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
            'name'        => 'required|string|max:100',
            'location'    => 'required|string|max:50|unique:menus,location,' . $this->route('menu')->id,
            'description' => 'nullable|string|max:255',
            'is_active'   => 'required|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Menü adı zorunludur.',
            'location.required' => 'Konum kodu zorunludur.',
            'location.unique'   => 'Bu konum kodu zaten kullanılıyor.',
        ];
    }
}
