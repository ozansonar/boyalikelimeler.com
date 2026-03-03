<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

final class PostStoreRequest extends FormRequest
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
            'title'       => ['required', 'string', 'max:200'],
            'body'        => ['required', 'string', 'min:50'],
            'excerpt'     => ['nullable', 'string', 'max:300'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
            'tags'        => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Yazı başlığı zorunludur.',
            'title.max'            => 'Başlık en fazla 200 karakter olabilir.',
            'body.required'        => 'Yazı içeriği zorunludur.',
            'body.min'             => 'Yazı içeriği en az 50 karakter olmalıdır.',
            'excerpt.max'          => 'Kısa açıklama en fazla 300 karakter olabilir.',
            'category_id.required' => 'Kategori seçimi zorunludur.',
            'category_id.exists'   => 'Seçilen kategori geçersiz.',
            'cover_image.image'    => 'Kapak görseli bir resim dosyası olmalıdır.',
            'cover_image.mimes'    => 'Kapak görseli JPG, PNG veya WebP formatında olmalıdır.',
            'cover_image.max'      => 'Kapak görseli en fazla 5MB olabilir.',
        ];
    }
}
