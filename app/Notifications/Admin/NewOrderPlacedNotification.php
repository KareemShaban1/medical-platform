<?php

namespace App\Notifications\Admin;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderPlacedNotification extends Notification
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $clinicName = $this->order->clinic?->name ?? ('#' . $this->order->clinic_id);

        return [
            'title' => 'New Order Placed',
            'message' => sprintf(
                'Order #%s has been placed by clinic "%s" with total %.2f.',
                $this->order->number,
                $clinicName,
                $this->order->total
            ),
            'order_id' => $this->order->id,
            'order_number' => $this->order->number,
            'clinic_id' => $this->order->clinic_id,
            'clinic_name' => $clinicName,
            'total' => $this->order->total,
            'payment_status' => $this->order->payment_status,
            'action_url' => route('admin.orders.show', $this->order->id),
            'type' => 'order_placed',
        ];
    }
}

