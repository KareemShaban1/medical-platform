<?php

namespace App\Notifications;

use App\Models\DoctorProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileApproved extends Notification
{
    use Queueable;

    protected $profile;

    /**
     * Create a new notification instance.
     */
    public function __construct(DoctorProfile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Congratulations! Your doctor profile has been approved.')
                    ->line('Your profile is now publicly visible on the website.')
                    ->action('View Profile', url('/clinic/doctor-profiles/' . $this->profile->id))
                    ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Profile Approved',
            'message' => 'Your doctor profile has been approved and is now publicly visible.',
            'profile_id' => $this->profile->id,
            'reviewer_name' => $this->profile->reviewer->name ?? 'Admin',
            'approved_at' => $this->profile->reviewed_at,
            'action_url' => route('clinic.doctor-profiles.show', $this->profile->id),
            'type' => 'profile_approved',
        ];
    }
}
