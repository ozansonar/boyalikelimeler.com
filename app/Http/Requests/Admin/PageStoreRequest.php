<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PageStoreRequest extends FormRequest
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
            'title'            => 'required|string|max:200',
            'excerpt'          => 'nullable|string|max:500',
            'body'             => 'required|string',
            'cover_image'      => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:170',
            'is_active'        => 'required|boolean',
            'sort_order'       => 'nullable|integer|min:0|max:999',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Sayfa başlığı zorunludur.',
            'title.max'            => 'Başlık en fazla 200 karakter olmalıdır.',
            'body.required'        => 'Sayfa içeriği zorunludur.',
            'cover_image.image'    => 'Kapak görseli bir resim dosyası olmalıdır.',
            'cover_image.mimes'    => 'Desteklenen formatlar: PNG, JPG, WebP.',
            'cover_image.max'      => 'Kapak görseli en fazla 1 MB olmalıdır.',
            'meta_title.max'       => 'Meta başlık en fazla 70 karakter olmalıdır.',
            'meta_description.max' => 'Meta açıklama en fazla 170 karakter olmalıdır.',
        ];
    }
}
