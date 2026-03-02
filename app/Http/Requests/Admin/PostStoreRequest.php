<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:200'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'category_id'      => ['required', 'exists:categories,id'],
            'status'           => ['required', Rule::enum(PostStatus::class)],
            'meta_title'       => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'is_featured'      => ['nullable', 'boolean'],
            'allow_comments'   => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0', 'max:999'],
            'published_at'     => ['nullable', 'date'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'İçerik başlığı zorunludur.',
            'title.max'            => 'Başlık en fazla 200 karakter olabilir.',
            'body.required'        => 'İçerik metni zorunludur.',
            'category_id.required' => 'Kategori seçimi zorunludur.',
            'category_id.exists'   => 'Geçersiz kategori.',
            'status.required'      => 'Yayın durumu zorunludur.',
            'cover_image.image'    => 'Kapak görseli geçerli bir resim dosyası olmalıdır.',
            'cover_image.max'      => 'Kapak görseli en fazla 1 MB olabilir.',
        ];
    }
}
