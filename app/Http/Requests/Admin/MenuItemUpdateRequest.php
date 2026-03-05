<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\LinkTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'target'     => ['required', new Enum(LinkTarget::class)],
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
