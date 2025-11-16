<?php

namespace App\Notifications\Supplier;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected Order $order;
    protected float $supplierTotal;

    public function __construct(Order $order, float $supplierTotal)
    {
        $this->order = $order;
        $this->supplierTotal = $supplierTotal;
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
        $clinicName = $this->order->clinic?->name ?? ('#' . $this->order->clinic_id);

        return [
            'type' => 'new_order',
            'title' => 'New Order Received',
            'message' => sprintf(
                'You have received a new order #%s from clinic "%s" totaling %.2f.',
                $this->order->number,
                $clinicName,
                $this->supplierTotal
            ),
            'order_id' => $this->order->id,
            'order_number' => $this->order->number,
            'clinic_id' => $this->order->clinic_id,
            'clinic_name' => $clinicName,
            'supplier_total' => $this->supplierTotal,
            'action_url' => route('supplier.orders.show', $this->order->id),
        ];
    }
}

