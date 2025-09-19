<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasUlids;
    const ATTACHMENTS = 1;  //defualt
    const IMAGES      = 2;

    protected $fillable = [
        'ulid',
        'disk',
        'path',
        'name',
        'extension',
        'size',
        'mime_type',
        'url',
        'type',
        'expired_at',
        'created_by',
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($attachment) {
            $attachment->created_by = auth()->user()->id;
        });
        static::deleting(function ($attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }

    public function uniqueIds()
    {
        return ['ulid'];
    }

    public function scopeMine($query)
    {
        return $query->where('created_by', auth()->user()->id);
    }

    public function scopeFor($model, $id)
    {
        $model->where('id', $id);
    }

    public function scopeOfType($model, $type)
    {
        $model->where('type', $type);
    }

    public function attachable()
    {
        return $this->morphTo();
    }
}
