<?php

namespace App\Notifications\Admin;

use App\Models\SupplierPayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupplierPayoutRequestedNotification extends Notification
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
        $supplier = $this->payoutRequest->supplier;
        $clinicName = $supplier->name ?? 'Unknown';

        return [
            'title' => __('New Supplier Payout Request'),
            'message' => __(':supplier has requested a payout of :amount', [
                'supplier' => $clinicName,
                'amount' => number_format((float) $this->payoutRequest->amount, 2) . ' ' . __('EGP'),
            ]),
            'payout_request_id' => $this->payoutRequest->id,
            'supplier_id' => $supplier->id ?? null,
            'amount' => $this->payoutRequest->amount,
            'type' => 'supplier_payout_request',
            'action_url' => route('admin.supplier-payouts.show', $this->payoutRequest->id),
        ];
    }
}
