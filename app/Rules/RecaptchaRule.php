<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(RecaptchaService::class);

        if (!$service->isEnabled()) {
            return;
        }

        if (!$service->verify($value)) {
            $fail('Lütfen robot olmadığınızı doğrulayın.');
        }
    }
}
