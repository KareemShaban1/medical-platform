<?php

namespace App\Notifications\Clinic;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RentalSpace;

class RentalSpaceApprovalStatusNotification extends Notification
{
    use Queueable;

    protected $rentalSpace;
    protected $status;
    protected $notes;

    public function __construct(RentalSpace $rentalSpace, string $status, ?string $notes = null)
    {
        $this->rentalSpace = $rentalSpace;
        $this->status = $status;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $statusText = ucfirst(str_replace('_', ' ', $this->status));

        return [
            'title' => 'Rental Space ' . $statusText,
            'message' => 'Your rental space "' . ($this->rentalSpace->name ?? 'Rental Space') . '" has been ' . strtolower($statusText) . '.',
            'rental_space_id' => $this->rentalSpace->id,
            'status' => $this->status,
            'notes' => $this->notes,
            'action_url' => route('clinic.rental-spaces.show', $this->rentalSpace->id),
            'type' => 'rental_space_status_update'
        ];
    }
}

