<?php

namespace App\Notifications\Supplier;

use App\Notifications\Channels\PhoneSMSChannel;
use App\Notifications\Messages\PhoneSmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierRegisteredNotification extends Notification
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
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    // public function toSMS(object $notifiable): PhoneSmsMessage
    // {
    //     return (new PhoneSmsMessage)
    //         ->content("Welcome to application " . config('app.name') . ". Your OTP is {$notifiable->otps->last()->otp} . Don't share this OTP with anyone.");
    // }
}
