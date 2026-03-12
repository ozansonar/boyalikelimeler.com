<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\LinkTarget;
use App\Enums\PageBoxType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'is_active'              => 'required|boolean',
            'sort_order'             => 'nullable|integer|min:0|max:999',
            'boxes'                  => 'nullable|array|max:20',
            'boxes.*.type'           => ['required', new Enum(PageBoxType::class)],
            'boxes.*.title'          => 'required|string|max:200',
            'boxes.*.description'    => 'nullable|string|max:1000',
            'boxes.*.link'           => 'nullable|string|max:500',
            'boxes.*.link_target'    => ['nullable', new Enum(LinkTarget::class)],
            'boxes.*.video_url'      => 'nullable|required_if:boxes.*.type,video|url|max:500',
            'boxes.*.col_desktop'    => 'required|integer|in:2,3,4,6,12',
            'boxes.*.col_tablet'     => 'required|integer|in:4,6,12',
            'boxes.*.col_mobile'     => 'required|integer|in:6,12',
            'box_images.*'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:1024',
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
            'meta_title.max'              => 'Meta başlık en fazla 70 karakter olmalıdır.',
            'meta_description.max'        => 'Meta açıklama en fazla 170 karakter olmalıdır.',
            'boxes.*.title.required'      => 'Kutu başlığı zorunludur.',
            'boxes.*.title.max'           => 'Kutu başlığı en fazla 200 karakter olmalıdır.',
            'boxes.*.description.max'     => 'Kutu açıklaması en fazla 1000 karakter olmalıdır.',
            'boxes.*.link.max'            => 'Bağlantı en fazla 500 karakter olmalıdır.',
            'boxes.*.video_url.required_if' => 'Video tipi seçildiğinde YouTube URL zorunludur.',
            'boxes.*.video_url.url'         => 'Geçerli bir YouTube URL giriniz.',
            'boxes.*.col_desktop.in'        => 'Geçersiz masaüstü boyut seçimi.',
            'boxes.*.col_tablet.in'       => 'Geçersiz tablet boyut seçimi.',
            'boxes.*.col_mobile.in'       => 'Geçersiz mobil boyut seçimi.',
            'box_images.*.image'          => 'Kutu görseli bir resim dosyası olmalıdır.',
            'box_images.*.mimes'          => 'Desteklenen formatlar: PNG, JPG, WebP.',
            'box_images.*.max'            => 'Kutu görseli en fazla 1 MB olmalıdır.',
        ];
    }
}
