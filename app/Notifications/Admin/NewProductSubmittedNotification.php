<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class NewProductSubmittedNotification extends Notification
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Product Submitted for Review',
            'message' => 'A new product "' . $this->product->name . '" has been submitted by ' . $this->product->supplier->name . ' for review.',
            'product_id' => $this->product->id,
            'supplier_id' => $this->product->supplier_id,
            'action_url' => route('admin.supplier-products.show', $this->product->id),
            'type' => 'product_submitted'
        ];
    }
}
