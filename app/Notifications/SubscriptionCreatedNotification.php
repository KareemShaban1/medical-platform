<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCreatedNotification extends Notification
{
    use Queueable;

    public Subscription $subscription;
    public bool $isAdmin;

    public function __construct(Subscription $subscription, bool $isAdmin = false)
    {
        $this->subscription = $subscription->loadMissing('plan', 'subscribable');
        $this->isAdmin = $isAdmin;
    }

    public function via($notifiable): array
    {
        // We currently use dedicated Mailable classes for email.
        // This notification is used for structured database records only.
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->isAdmin) {
            return (new MailMessage)
                ->subject(__('New subscription created'))
                ->view('emails.subscription.admin-created', [
                    'subscription' => $this->subscription,
                ]);
        }

        return (new MailMessage)
            ->subject(__('Your subscription is active'))
            ->view('emails.subscription.user-created', [
                'subscription' => $this->subscription,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $plan = $this->subscription->plan;
        $entity = $this->subscription->subscribable;

        return [
            'subscription_id' => $this->subscription->id,
            'plan_id' => $plan->id ?? null,
            'plan_name' => $plan->name ?? null,
            'plan_type' => $plan->plan_type ?? null,
            'plan_level' => $plan->level ?? null,
            'price' => $plan->price ?? null,
            'status' => $this->subscription->status,
            'subscribable_type' => $this->subscription->subscribable_type,
            'subscribable_id' => $this->subscription->subscribable_id,
            'subscribable_name' => $entity->name ?? null,
            'is_admin_notification' => $this->isAdmin,
        ];
    }
}
