<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EntityRegisteredNotification extends Notification
{
    protected string $entityType;
    protected int $entityId;
    protected string $entityName;

    /**
     * @param string $entityType  Readable type e.g. "Clinic" or "Supplier"
     * @param int    $entityId
     * @param string $entityName
     */
    public function __construct(string $entityType, int $entityId, string $entityName)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->entityName = $entityName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'entity_name' => $this->entityName,
            'action_url' => $this->entityType == 'Clinic' ? url('/admin/clinics') : url('/admin/suppliers'),
            'message' => __('New :type registered: :name', [
                'type' => $this->entityType,
                'name' => $this->entityName,
            ]),
        ];
    }
}

