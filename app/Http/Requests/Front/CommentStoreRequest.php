<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

final class CommentStoreRequest extends FormRequest
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
        $rules = [
            'commentable_type'     => ['required', 'string', 'in:literary_work,post'],
            'commentable_id'       => ['required', 'integer', 'min:1'],
            'body'                 => ['required', 'string', 'min:10', 'max:3000'],
            'rating'               => ['required', 'integer', 'min:1', 'max:5'],
            'g-recaptcha-response' => ['sometimes', new RecaptchaRule()],
        ];

        if (!$this->user()) {
            $rules['first_name'] = ['required', 'string', 'max:100'];
            $rules['last_name']  = ['required', 'string', 'max:100'];
            $rules['email']      = ['required', 'email', 'max:255'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.max'      => 'Ad en fazla 100 karakter olabilir.',
            'last_name.required'  => 'Soyad alanı zorunludur.',
            'last_name.max'       => 'Soyad en fazla 100 karakter olabilir.',
            'email.required'      => 'E-posta adresi zorunludur.',
            'email.email'         => 'Geçerli bir e-posta adresi giriniz.',
            'body.required'       => 'Yorum alanı zorunludur.',
            'body.min'            => 'Yorum en az 10 karakter olmalıdır.',
            'body.max'            => 'Yorum en fazla 3000 karakter olabilir.',
            'rating.required'     => 'Lütfen bir puan seçiniz.',
            'rating.min'          => 'Puan en az 1 olmalıdır.',
            'rating.max'          => 'Puan en fazla 5 olabilir.',
        ];
    }
}
