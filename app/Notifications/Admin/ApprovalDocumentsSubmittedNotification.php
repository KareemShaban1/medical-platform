<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class ApprovalDocumentsSubmittedNotification extends Notification
{
    protected string $entityType;
    protected int $entityId;
    protected string $entityName;

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
        $actionUrl = $this->entityType === 'Clinic'
            ? route('admin.clinics.approval', $this->entityId)
            : route('admin.suppliers.approval', $this->entityId);

        return [
            'title' => 'Documents Submitted for Approval',
            'message' => $this->entityType.' '.$this->entityName.' submitted documents for approval.',
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'entity_name' => $this->entityName,
            'action_url' => $actionUrl,
            'type' => 'approval_documents_submitted',
        ];
    }
}
