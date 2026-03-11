<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class RecaptchaService
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /**
     * Check if reCAPTCHA is enabled in settings.
     */
    public function isEnabled(): bool
    {
        $settings = $this->getSettings();

        return ($settings['enabled'] ?? '0') === '1'
            && !empty($settings['site_key'])
            && !empty($settings['secret_key']);
    }

    /**
     * Get the reCAPTCHA site key (public key).
     */
    public function getSiteKey(): ?string
    {
        return $this->getSettings()['site_key'] ?? null;
    }

    /**
     * Verify the reCAPTCHA response token.
     */
    public function verify(?string $token): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $secretKey = $this->getSettings()['secret_key'] ?? '';

        if (empty($secretKey)) {
            return true;
        }

        $response = Http::asForm()->post(self::VERIFY_URL, [
            'secret'   => $secretKey,
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        return $response->successful() && ($response->json('success') === true);
    }

    /**
     * @return array<string, string|null>
     */
    private function getSettings(): array
    {
        return $this->settingService->getGroup('recaptcha');
    }
}
