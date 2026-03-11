<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PollUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question'   => ['required', 'string', 'max:300'],
            'is_active'  => ['nullable'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
            'options'    => ['required', 'array', 'min:2', 'max:5'],
            'options.*'  => ['required', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required'     => 'Anket sorusu zorunludur.',
            'question.max'          => 'Anket sorusu en fazla 300 karakter olabilir.',
            'options.required'      => 'En az 2 şık eklemelisiniz.',
            'options.min'           => 'En az 2 şık eklemelisiniz.',
            'options.max'           => 'En fazla 5 şık ekleyebilirsiniz.',
            'options.*.required'    => 'Şık metni boş bırakılamaz.',
            'options.*.max'         => 'Şık metni en fazla 200 karakter olabilir.',
            'ends_at.after_or_equal' => 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.',
        ];
    }
}
