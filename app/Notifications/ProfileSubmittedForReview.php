<?php

namespace App\Notifications;

use App\Models\DoctorProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileSubmittedForReview extends Notification
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
                    ->line('A new doctor profile has been submitted for review.')
                    ->line('Doctor: ' . $this->profile->name)
                    ->action('Review Profile', url('/admin/doctor-profiles/' . $this->profile->id))
                    ->line('Please review and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Profile Submitted for Review',
            'message' => 'Dr. ' . $this->profile->name . ' has submitted their profile for review.',
            'profile_id' => $this->profile->id,
            'doctor_name' => $this->profile->name,
            'clinic_user_id' => $this->profile->clinic_user_id,
            'action_url' => route('admin.doctor-profiles.show', $this->profile->id),
            'type' => 'profile_submitted',
        ];
    }
}
