<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $token,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage())
            ->subject('Şifre Sıfırlama Talebi — Boyalı Kelimeler')
            ->greeting('Merhaba ' . $notifiable->name . '!')
            ->line('Hesabınız için şifre sıfırlama talebinde bulunuldu. Aşağıdaki butona tıklayarak yeni şifrenizi belirleyebilirsiniz.')
            ->action('Şifremi Sıfırla', $resetUrl)
            ->line('Bu link 60 dakika içinde geçerliliğini yitirecektir.')
            ->line('Eğer bu talebi siz yapmadıysanız herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation('Saygılarımızla, Boyalı Kelimeler');
    }
}
