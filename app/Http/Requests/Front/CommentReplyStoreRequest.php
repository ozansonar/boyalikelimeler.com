<?php

declare(strict_types=1);

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

final class CommentReplyStoreRequest extends FormRequest
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
            'comment_id' => ['required', 'integer', 'exists:comments,id'],
            'body'       => ['required', 'string', 'min:10', 'max:3000'],
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
            'comment_id.required' => 'Yanıtlanacak yorum bulunamadı.',
            'comment_id.exists'   => 'Yanıtlanacak yorum bulunamadı.',
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.max'      => 'Ad en fazla 100 karakter olabilir.',
            'last_name.required'  => 'Soyad alanı zorunludur.',
            'last_name.max'       => 'Soyad en fazla 100 karakter olabilir.',
            'email.required'      => 'E-posta adresi zorunludur.',
            'email.email'         => 'Geçerli bir e-posta adresi giriniz.',
            'body.required'       => 'Yanıt alanı zorunludur.',
            'body.min'            => 'Yanıt en az 10 karakter olmalıdır.',
            'body.max'            => 'Yanıt en fazla 3000 karakter olabilir.',
        ];
    }
}
