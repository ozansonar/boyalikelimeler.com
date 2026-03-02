<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $newUser,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Yeni Kullanıcı Kaydı — Boyalı Kelimeler')
            ->greeting('Merhaba ' . $notifiable->name . '!')
            ->line('Siteye yeni bir kullanıcı kayıt oldu.')
            ->line('**Ad Soyad:** ' . $this->newUser->name)
            ->line('**E-posta:** ' . $this->newUser->email)
            ->line('**Kayıt Tarihi:** ' . $this->newUser->created_at->format('d.m.Y H:i'))
            ->action('Admin Paneline Git', url(route('admin.dashboard', [], false)))
            ->salutation('Boyalı Kelimeler Sistem Bildirimi');
    }
}
