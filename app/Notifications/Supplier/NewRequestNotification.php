<?php

namespace App\Notifications\Supplier;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRequestNotification extends Notification
{
    use Queueable;

    protected $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->subject('New Request Available - ' . $this->request->category->name)
    //         ->greeting('Hello ' . $notifiable->name . '!')
    //         ->line('A new request has been posted in your specialized category: ' . $this->request->category->name)
    //         ->line('Request Details:')
    //         ->line('- Clinic: ' . $this->request->clinic->name)
    //         ->line('- Quantity: ' . $this->request->quantity)
    //         ->line('- Description: ' . \Str::limit($this->request->description, 100))
    //         ->action('View Request Details', route('supplier.available-requests.show', $this->request->id))
    //         ->line('Submit your offer to compete for this opportunity!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_request',
            'title' => 'New Request Available',
            'message' => 'A new request has been posted in ' . $this->request->categories->pluck('name')->join(', ') . ' category',
            'request_id' => $this->request->id,
            'clinic_name' => $this->request->clinic?->name,
            'category_name' => $this->request->categories->pluck('name')->join(', '),
            'action_url' => route('supplier.available-requests.show', $this->request->id),
        ];
    }
}
