<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'badge_icon'    => ['nullable', 'string', 'max:100'],
            'badge_text'    => ['required', 'string', 'max:100'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'max:500'],
            'button_text'   => ['nullable', 'string', 'max:100'],
            'button_link'   => ['nullable', 'url', 'max:500'],
            'button_target' => ['required', 'in:_blank,_self'],
            'is_active'     => ['nullable', 'boolean'],
            'sort_order'    => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'badge_text.required'    => 'Rozet metni zorunludur.',
            'badge_text.max'         => 'Rozet metni en fazla 100 karakter olabilir.',
            'title.required'         => 'Başlık zorunludur.',
            'title.max'              => 'Başlık en fazla 255 karakter olabilir.',
            'description.required'   => 'Açıklama zorunludur.',
            'description.max'        => 'Açıklama en fazla 500 karakter olabilir.',
            'button_text.max'        => 'Buton metni en fazla 100 karakter olabilir.',
            'button_link.url'        => 'Geçerli bir URL girin.',
            'button_link.max'        => 'Buton linki en fazla 500 karakter olabilir.',
            'button_target.required' => 'Link hedefi zorunludur.',
            'button_target.in'       => 'Geçerli bir link hedefi seçin.',
        ];
    }
}
