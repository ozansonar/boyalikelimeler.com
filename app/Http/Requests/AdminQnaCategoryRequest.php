<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminQnaCategoryRequest extends FormRequest
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
        $categoryId = $this->route('id');

        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', Rule::unique('qna_categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon'        => ['required', 'string', 'max:100'],
            'color_class' => ['required', 'string', 'max:100'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'Kategori adı zorunludur.',
            'slug.required'  => 'Slug zorunludur.',
            'slug.unique'    => 'Bu slug zaten kullanılıyor.',
            'icon.required'  => 'İkon zorunludur.',
            'color_class.required' => 'Renk sınıfı zorunludur.',
        ];
    }
}
