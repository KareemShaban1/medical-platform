<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementDismissal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'announcement_id',
        'dismissable_type',
        'dismissable_id',
        'dismissed_at',
    ];

    protected $casts = [
        'dismissed_at' => 'datetime',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}

