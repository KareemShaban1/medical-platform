<?php

namespace App\Notifications\Supplier;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class ProductApprovalStatusNotification extends Notification
{
    use Queueable;

    protected $product;
    protected $status;
    protected $notes;

    public function __construct(Product $product, $status, $notes = null)
    {
        $this->product = $product;
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
            'title' => 'Product ' . $statusText,
            'message' => 'Your product "' . $this->product->name . '" has been ' . strtolower($statusText) . '.',
            'product_id' => $this->product->id,
            'status' => $this->status,
            'notes' => $this->notes,
            'action_url' => route('supplier.products.show', $this->product->id),
            'type' => 'product_status_update'
        ];
    }
}
