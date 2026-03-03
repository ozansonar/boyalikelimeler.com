<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditorImageUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:1024'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Görsel dosyası gereklidir.',
            'file.image'    => 'Yüklenen dosya bir görsel olmalıdır.',
            'file.mimes'    => 'Sadece JPG, PNG, GIF ve WebP formatları desteklenir.',
            'file.max'      => 'Görsel boyutu en fazla 1 MB olabilir.',
        ];
    }
}
