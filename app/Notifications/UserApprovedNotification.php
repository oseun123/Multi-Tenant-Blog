<?php



namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserApprovedNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        $link = env('FRONTEND_URL');
        return (new MailMessage)
            ->subject('Your Account Has Been Approved')
            ->line('Congratulations! Your account has been approved by the admin.')
            ->line('You can now log in and start using the platform.')
            ->action('Login', $link)
            ->line('Thank you for joining us!');
    }
}
