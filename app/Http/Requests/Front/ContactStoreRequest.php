<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use App\Enums\ContactSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class ContactStoreRequest extends FormRequest
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
            'fullname' => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255'],
            'subject'  => ['required', 'string', new Enum(ContactSubject::class)],
            'message'  => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fullname.required' => 'Ad soyad alanı zorunludur.',
            'fullname.max'      => 'Ad soyad en fazla 100 karakter olabilir.',
            'email.required'    => 'E-posta adresi zorunludur.',
            'email.email'       => 'Geçerli bir e-posta adresi giriniz.',
            'subject.required'  => 'Konu seçimi zorunludur.',
            'subject.in'        => 'Geçersiz konu seçimi.',
            'message.required'  => 'Mesaj alanı zorunludur.',
            'message.min'       => 'Mesaj en az 10 karakter olmalıdır.',
            'message.max'       => 'Mesaj en fazla 5000 karakter olabilir.',
        ];
    }
}
