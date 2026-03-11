<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\AdvertisementPosition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdvertisementStoreRequest extends FormRequest
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
            'image'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
            'image.required'          => 'Reklam görseli zorunludur.',
            'image.image'             => 'Geçerli bir görsel dosyası yükleyin.',
            'image.max'               => 'Görsel en fazla 2 MB olabilir.',
            'link.url'                => 'Geçerli bir URL girin.',
            'end_date.after_or_equal' => 'Bitiş tarihi, başlangıç tarihinden önce olamaz.',
        ];
    }
}
