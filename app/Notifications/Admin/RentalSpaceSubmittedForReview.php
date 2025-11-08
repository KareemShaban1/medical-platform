<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RentalSpace;

class RentalSpaceSubmittedForReview extends Notification
{
    use Queueable;

    protected $rentalSpace;

    public function __construct(RentalSpace $rentalSpace)
    {
        $this->rentalSpace = $rentalSpace;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Rental Space Submitted',
            'message' => 'Clinic submitted "' . ($this->rentalSpace->name ?? 'Rental Space') . '" for review.',
            'rental_space_id' => $this->rentalSpace->id,
            'clinic_id' => $this->rentalSpace->clinic_id,
            'action_url' => route('admin.rental-spaces.show', $this->rentalSpace->id),
            'type' => 'rental_space_submitted'
        ];
    }
}

