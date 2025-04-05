<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminPasswordResetNotification extends Notification
{
    protected $token;


    public function __construct($token)
    {
        $this->token = $token;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {

        $resetUrl = env('FRONTEND_URL') . '/reset-password' . '?token=' . $this->token . '&email=' . $notifiable->email;
        return (new MailMessage)
            ->subject('Password Reset Request')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This reset link will expire in 1 hour.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
