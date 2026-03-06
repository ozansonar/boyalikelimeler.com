<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use App\Enums\LiteraryWorkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class LiteraryWorkStoreRequest extends FormRequest
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
            'title'                => ['required', 'string', 'max:200'],
            'work_type'            => ['required', new Enum(LiteraryWorkType::class)],
            'body'                 => ['required', 'string', 'min:50'],
            'excerpt'              => ['nullable', 'string', 'max:300'],
            'literary_category_id' => ['required', 'integer', 'exists:literary_categories,id'],
            'cover_image'          => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'work_type.required'            => 'Eser türü seçimi zorunludur.',
            'work_type.Illuminate\Validation\Rules\Enum' => 'Geçersiz eser türü seçildi.',
            'title.required'                => 'Eser başlığı zorunludur.',
            'title.max'                     => 'Başlık en fazla 200 karakter olabilir.',
            'body.required'                 => 'Eser içeriği zorunludur.',
            'body.min'                      => 'Eser içeriği en az 50 karakter olmalıdır.',
            'excerpt.max'                   => 'Kısa açıklama en fazla 300 karakter olabilir.',
            'literary_category_id.required' => 'Kategori seçimi zorunludur.',
            'literary_category_id.exists'   => 'Seçilen kategori geçersiz.',
            'cover_image.image'             => 'Kapak görseli bir resim dosyası olmalıdır.',
            'cover_image.mimes'             => 'Kapak görseli JPG, PNG veya WebP formatında olmalıdır.',
            'cover_image.max'               => 'Kapak görseli en fazla 2 MB olabilir.',
        ];
    }
}
