<?php

namespace App\Notifications\Supplier;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferDeclinedNotification extends Notification
{
    use Queueable;

    protected $offer;

    /**
     * Create a new notification instance.
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
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
    // public function toMail(object $notifiable): MailMessage
    // {
    //     $reason = $this->offer->request->status === 'canceled' ? 'request was canceled' : 'another offer was selected';

    //     return (new MailMessage)
    //         ->subject('Offer Update - ' . ucfirst($this->offer->status))
    //         ->greeting('Hello ' . $notifiable->name . '!')
    //         ->line('We wanted to inform you that your offer for the request from ' . $this->offer->request->clinic->name . ' has been declined.')
    //         ->line('Reason: The ' . $reason . '.')
    //         ->line('Offer Details:')
    //         ->line('- Request: ' . \Str::limit($this->offer->request->description, 100))
    //         ->line('- Your Price: $' . number_format($this->offer->final_price, 2))
    //         ->action('View Offer Details', route('supplier.offers.show', $this->offer->id))
    //         ->line('Don\'t worry! Keep an eye out for new opportunities that match your expertise.');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reason = $this->offer->request->status === 'canceled' ? 'request was canceled' : 'another offer was selected';

        return [
            'type' => 'offer_declined',
            'title' => 'Offer Declined',
            'message' => 'Your offer has been declined because the ' . $reason,
            'offer_id' => $this->offer->id,
            'request_id' => $this->offer->request->id,
            'clinic_name' => $this->offer->request->clinic->name,
            'reason' => $reason,
            'action_url' => route('supplier.offers.show', $this->offer->id),
        ];
    }
}
