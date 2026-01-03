<?php

namespace App\Notifications\Supplier;

use App\Models\SupplierPayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupplierPayoutPaidNotification extends Notification
{
    use Queueable;

    public SupplierPayoutRequest $payoutRequest;

    public function __construct(SupplierPayoutRequest $payoutRequest)
    {
        $this->payoutRequest = $payoutRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => __('Payout Processed'),
            'message' => __('Your payout request of :amount has been processed.', [
                'amount' => number_format((float) $this->payoutRequest->amount, 2) . ' ' . __('EGP'),
            ]),
            'payout_request_id' => $this->payoutRequest->id,
            'amount' => $this->payoutRequest->amount,
            'type' => 'supplier_payout_paid',
            'action_url' => route('supplier.payouts.show', $this->payoutRequest->id),
        ];
    }
}
