<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class WriterApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'motivation' => ['required', 'string', 'min:50', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'motivation.required' => 'Motivasyon metni zorunludur.',
            'motivation.min'      => 'Motivasyon metni en az 50 karakter olmalıdır.',
            'motivation.max'      => 'Motivasyon metni en fazla 1000 karakter olabilir.',
        ];
    }
}
