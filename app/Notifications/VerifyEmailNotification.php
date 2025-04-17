<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->view('user.verify_email', ['verificationUrl' => $url]);
    }

    protected function verificationUrl($notifiable)
    {
        $token = sha1($notifiable->getEmailForVerification());
        $user = $notifiable->getKey();
        $expireAt = now()->addMinutes(config('auth.verification.expire', 60))->timestamp;

        return config('auth.verify_email_url') . "?token=$token&user=$user&expire_at=$expireAt";
    }
}
