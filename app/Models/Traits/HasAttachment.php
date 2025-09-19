<?php

namespace App\Models\Traits;

use App\Http\Services\FileService;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;

trait HasAttachment
{
    public function attachments($type = null)
    {
        return $this->morphMany(Attachment::class, 'attachable')->ofType($type ?? Attachment::ATTACHMENTS);
    }

    public function attachFile(UploadedFile $file, string $path = 'attachments', string $disk = 'public', $type = null, $name = null, $expired_at = null)
    {
        $file = FileService::storeFile($file, $path, $disk);

        return $this->attachments()->create([
            'disk'       => $file->disk,
            'path'       => $file->path,
            'name'       => $name ?? $file->name,
            'type'       => $type ?? Attachment::ATTACHMENTS,
            'size'       => $file->size,
            'mime_type'  => $file->mime_type,
            'url'        => $file->url,
            'expired_at' => $expired_at ?? null,
        ]);
    }

    public function attachAllFiles(array $files, string $path = 'attachments', string $disk = 'public')
    {
        $attachments = [];
        foreach ($files as $file) {
            $attachments[] = $this->attachFile($file, $path, $disk);
        }
        return $attachments;
    }

    public function detachFile(Attachment|null $attachment)
    {
        if ($attachment) {
            FileService::deleteFile($attachment->path, $attachment->disk);
            return $attachment->delete();
        }
        return false;
    }

    public function detachAllFiles(): bool
    {
        $this->attachments->each(function ($attachment) {
            $this->detachFile($attachment);
        });
        return true;
    }
}
