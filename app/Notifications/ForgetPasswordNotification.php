<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;

class ForgetPasswordNotification extends Notification implements ShouldQueue
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
        return config('auth.password_forget_url') . "?toke=$this->token&email=$this->email";
    }
}
