<?php

namespace App\Notifications\Clinic;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOfferNotification extends Notification
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Offer Received for Your Request')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new offer for your request in ' . $this->offer->request->category->name . ' category.')
            ->line('Offer Details:')
            ->line('- Supplier: ' . $this->offer->supplier->name)
            ->line('- Price: $' . number_format($this->offer->price, 2))
            ->line('- Discount: $' . number_format($this->offer->discount ?? 0, 2))
            ->line('- Final Price: $' . number_format($this->offer->final_price, 2))
            ->line('- Delivery Time: ' . $this->offer->delivery_time->format('Y-m-d'))
            ->action('View Request & All Offers', route('clinic.requests.show', $this->offer->request->id))
            ->line('Review all offers and choose the best one for your needs.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_offer',
            'title' => 'New Offer Received',
            'message' => 'You received a new offer from ' . $this->offer->supplier->name . ' for $' . number_format($this->offer->final_price, 2),
            'offer_id' => $this->offer->id,
            'request_id' => $this->offer->request->id,
            'supplier_name' => $this->offer->supplier->name,
            'final_price' => $this->offer->final_price,
            'delivery_time' => $this->offer->delivery_time->format('Y-m-d'),
            'action_url' => route('clinic.requests.show', $this->offer->request->id),
        ];
    }
}
