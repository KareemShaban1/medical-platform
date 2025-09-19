<?php

namespace App\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public static function storeFile(UploadedFile $file, string $path = 'attachments', string $disk = 'public')
    {
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs($path, $filename, $disk);

        return (object) [
            'disk' => $disk,
            'path' => storage_path($path . '/' . $filename),
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'url' => $path . '/' . $filename,
            // 'url' => Storage::disk($disk)->url($path . '/' . $filename),
        ];
    }

    public static function deleteFile($path, $disk = 'public')
    {
        return Storage::disk($disk)->delete($path);
    }
}
