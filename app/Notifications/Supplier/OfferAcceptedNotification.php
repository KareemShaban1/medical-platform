<?php

namespace App\Notifications\Supplier;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferAcceptedNotification extends Notification
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
    //     return (new MailMessage)
    //         ->subject('Congratulations! Your Offer Has Been Accepted')
    //         ->greeting('Hello ' . $notifiable->name . '!')
    //         ->line('Great news! Your offer has been accepted by ' . $this->offer->request->clinic->name)
    //         ->line('Offer Details:')
    //         ->line('- Request: ' . \Str::limit($this->offer->request->description, 100))
    //         ->line('- Accepted Price: $' . number_format($this->offer->final_price, 2))
    //         ->line('- Delivery Date: ' . $this->offer->delivery_time->format('Y-m-d'))
    //         ->action('View Offer Details', route('supplier.offers.show', $this->offer->id))
    //         ->line('Please prepare to fulfill this order according to your terms and conditions.');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_accepted',
            'title' => 'Offer Accepted!',
            'message' => 'Your offer has been accepted by ' . $this->offer->request->clinic->name,
            'offer_id' => $this->offer->id,
            'request_id' => $this->offer->request->id,
            'clinic_name' => $this->offer->request->clinic->name,
            'final_price' => $this->offer->final_price,
            'action_url' => route('supplier.offers.show', $this->offer->id),
        ];
    }
}
