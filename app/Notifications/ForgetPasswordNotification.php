<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ForgetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private string $token, private string $email)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('user.password_forget', ['passwordForgetUrl' => $this->passwordForgetUrl()]);
    }


    protected function passwordForgetUrl()
    {
        return URL::temporarySignedRoute(
            'password_reset_check',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'token' => $this->token,
                'email' => $this->email,
            ]
        );
    }
}
