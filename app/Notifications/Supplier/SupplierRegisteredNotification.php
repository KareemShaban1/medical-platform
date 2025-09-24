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
    protected $otp;
    /**
     * Create a new notification instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
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
        ->subject('Supplier Registration - Email Verification Required')
        ->greeting('Hello ' . $notifiable->name . '!')
        ->line('Welcome to ' . config('app.name') . '!')
        ->line('Thank you for registering as a supplier. To complete your registration, please verify your email address using the OTP below:')
        ->line('**Your OTP Code: ' . $this->otp->otp . '**')
        ->line('This OTP will expire in 5 minutes.')
        ->line('⚠️ **Important:** Do not share this OTP with anyone.')
        ->line('Once verified, your supplier account will be automatically approved and you can access your dashboard.')
        ->salutation('Best regards,<br>' . config('app.name') . ' Team');

    }
}
