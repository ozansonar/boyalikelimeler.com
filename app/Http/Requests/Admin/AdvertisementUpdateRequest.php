<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\AdvertisementPosition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdvertisementUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:200'],
            'position'    => ['required', Rule::enum(AdvertisementPosition::class)],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:3072', 'dimensions:max_width=1400'],
            'link'        => ['nullable', 'url', 'max:500'],
            'link_target' => ['required', 'in:_blank,_self'],
            'is_active'   => ['nullable', 'boolean'],
            'start_date'  => ['nullable', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Reklam başlığı zorunludur.',
            'title.max'               => 'Başlık en fazla 200 karakter olabilir.',
            'position.required'       => 'Pozisyon seçimi zorunludur.',
            'image.image'             => 'Geçerli bir görsel dosyası yükleyin.',
            'image.mimes'             => 'Yalnızca JPG, PNG, WebP veya GIF formatları kabul edilir.',
            'image.max'               => 'Görsel en fazla 3 MB olabilir.',
            'image.dimensions'        => 'Görselin genişliği en fazla 1400 piksel olabilir.',
            'link.url'                => 'Geçerli bir URL girin.',
            'end_date.after_or_equal' => 'Bitiş tarihi, başlangıç tarihinden önce olamaz.',
        ];
    }
}
