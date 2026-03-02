<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage())
            ->subject('E-posta Adresinizi Doğrulayın — Boyalı Kelimeler')
            ->greeting('Merhaba ' . $notifiable->name . '!')
            ->line('Boyalı Kelimeler topluluğuna hoş geldiniz! Hesabınızı aktif hale getirmek için lütfen aşağıdaki butona tıklayın.')
            ->action('E-posta Adresimi Doğrula', $verificationUrl)
            ->line('Bu link 60 dakika içinde geçerliliğini yitirecektir.')
            ->line('Eğer bu hesabı siz oluşturmadıysanız herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation('Saygılarımızla, Boyalı Kelimeler');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
