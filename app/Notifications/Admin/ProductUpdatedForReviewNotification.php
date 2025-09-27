<?php

namespace App\Notifications\Admin;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductUpdatedForReviewNotification extends Notification
{
    use Queueable;

    protected $product;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Product Updated for Review',
            'message' => 'The product "' . $this->product->name . '" has been updated for review.',
            'product_id' => $this->product->id,
            'action_url' => route('admin.supplier-products.show', $this->product->id),
            'type' => 'product_updated_for_review',
        ];
    }
}
