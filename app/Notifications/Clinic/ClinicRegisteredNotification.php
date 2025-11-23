<?php

namespace App\Notifications\Clinic;

use App\Notifications\Channels\PhoneSMSChannel;
use App\Notifications\Messages\PhoneSmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClinicRegisteredNotification extends Notification
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
            ->subject(__('Clinic Registration - Email Verification'))
            ->view('emails.auth.otp', [
                'title' => __('Verify your clinic email'),
                'subtitle' => __('Secure your clinic on :app', ['app' => config('app.name')]),
                'intro' => __('Use the one-time password below to verify your email and activate your clinic account.'),
                'otp' => $this->otp->otp,
                'name' => $notifiable->name,
                'expiryText' => __('This code expires in :minutes minutes.', ['minutes' => 5]),
                'warningText' => __('Do not share this code with anyone.'),
                'ctaUrl' => url('/clinic/dashboard'),
                'ctaLabel' => __('Open clinic dashboard'),
                'appName' => config('app.name'),
            ]);
    }
}
