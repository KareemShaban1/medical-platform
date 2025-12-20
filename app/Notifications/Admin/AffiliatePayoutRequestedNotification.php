<?php

namespace App\Notifications\Admin;

use App\Models\AffiliatePayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AffiliatePayoutRequestedNotification extends Notification
{
    use Queueable;

    protected AffiliatePayoutRequest $payoutRequest;

    public function __construct(AffiliatePayoutRequest $payoutRequest)
    {
        $this->payoutRequest = $payoutRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $code = $this->payoutRequest->affiliateCode;
        $owner = $code?->affiliateable;
        $ownerName = $owner?->name ?? $owner?->email ?? __('Unknown');

        return [
            'affiliate_code' => $code?->code,
            'amount' => $this->payoutRequest->amount,
            'owner_name' => $ownerName,
            'action_url' => url('/admin/affiliates/payouts'),
            'message' => __('New payout request from :name', ['name' => $ownerName]),
        ];
    }
}
