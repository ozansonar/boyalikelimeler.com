<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PwaInstallPlatform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PwaInstallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => ['nullable', Rule::enum(PwaInstallPlatform::class)],
            'referrer' => ['nullable', 'string', 'max:500'],
        ];
    }
}
