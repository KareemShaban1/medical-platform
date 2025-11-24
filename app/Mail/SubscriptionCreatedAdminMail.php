<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCreatedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription->loadMissing('plan', 'subscribable');
    }

    public function build(): self
    {
        return $this->subject(__('New subscription created'))
            ->view('emails.subscription.admin-created');
    }
}

