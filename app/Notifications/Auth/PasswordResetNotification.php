<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    protected $otp;
    protected $userType;

    /**
     * Create a new notification instance.
     */
    public function __construct($otp, string $userType = 'user')
    {
        $this->otp = $otp;
        $this->userType = $userType;
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
        $userTypeLabel = ucfirst($this->userType);

        return (new MailMessage)
            ->subject(__('Password Reset - Verification Code'))
            ->view('emails.auth.otp', [
                'title' => __('Reset Your Password'),
                'subtitle' => __('Password reset request for :app', ['app' => config('app.name')]),
                'intro' => __('Use the one-time password below to reset your password. If you did not request a password reset, please ignore this email.'),
                'otp' => $this->otp->otp,
                'name' => $notifiable->name,
                'expiryText' => __('This code expires in :minutes minutes.', ['minutes' => 5]),
                'warningText' => __('Do not share this code with anyone.'),
                'appName' => config('app.name'),
            ]);
    }
}
