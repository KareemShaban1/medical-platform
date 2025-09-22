<?php

namespace App\Notifications;

use App\Models\DoctorProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileRejected extends Notification
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
                    ->line('Your doctor profile has been rejected.')
                    ->line('Reason: ' . $this->profile->rejection_reason)
                    ->line('You can edit your profile and resubmit for review.')
                    ->action('Edit Profile', url('/clinic/doctor-profiles/' . $this->profile->id))
                    ->line('Please address the feedback and resubmit.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Profile Rejected',
            'message' => 'Your doctor profile has been rejected. Please review the feedback and make necessary changes.',
            'profile_id' => $this->profile->id,
            'rejection_reason' => $this->profile->rejection_reason,
            'reviewer_name' => $this->profile->reviewer->name ?? 'Admin',
            'rejected_at' => $this->profile->reviewed_at,
            'action_url' => route('clinic.doctor-profiles.show', $this->profile->id),
            'type' => 'profile_rejected',
        ];
    }
}
