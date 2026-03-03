<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class LiteraryWorkRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Revize sebebi zorunludur.',
            'reason.min'      => 'Revize sebebi en az 10 karakter olmalıdır.',
            'reason.max'      => 'Revize sebebi en fazla 2000 karakter olabilir.',
        ];
    }
}
