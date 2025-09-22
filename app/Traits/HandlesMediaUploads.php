<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia;

trait HandlesMediaUploads
{
    /**
     * Process media uploads for single or multiple file fields.
     *
     * Each definition item should be an array with keys:
     * - field: request file field name
     * - collection: media collection name
     * - multiple: bool, whether to treat the field as multiple files (default false)
     */
    protected function processMedia(HasMedia $model, Request $request, array $definitions, string $action): void
    {
        foreach ($definitions as $def) {
            $field = $def['field'] ?? null;
            $collection = $def['collection'] ?? null;
            $multiple = (bool)($def['multiple'] ?? false);

            if (!$field || !$collection) {
                continue;
            }

            if ($request->hasFile($field)) {
                if ($action === 'updated') {
                    // Clear existing media when updating
                    $model->clearMediaCollection($collection);
                }

                if ($multiple) {
                    foreach ((array)$request->file($field) as $file) {
                        if ($file) {
                            $model->addMedia($file)->toMediaCollection($collection);
                        }
                    }
                } else {
                    $file = $request->file($field);
                    if ($file) {
                        $model->addMedia($file)->toMediaCollection($collection);
                    }
                }
            }
        }
    }

    /**
     * Clear all specified media collections for the given model.
     */
    protected function clearAllMedia(HasMedia $model, array $collections): void
    {
        foreach ($collections as $collection) {
            $model->clearMediaCollection($collection);
        }
    }
}
