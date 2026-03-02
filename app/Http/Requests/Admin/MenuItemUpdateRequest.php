<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemUpdateRequest extends FormRequest
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
            'title'      => 'required|string|max:100',
            'url'        => 'required|string|max:500',
            'icon'       => 'nullable|string|max:100',
            'target'     => 'required|in:_self,_blank',
            'parent_id'  => 'nullable|exists:menu_items,id',
            'is_active'  => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Menü öğesi başlığı zorunludur.',
            'url.required'   => 'URL adresi zorunludur.',
            'target.in'      => 'Geçersiz hedef değeri.',
        ];
    }
}
