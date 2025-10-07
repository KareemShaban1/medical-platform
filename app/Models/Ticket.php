<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CLOSED = 'closed';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    const TYPE_REFUND = 'refund';
    const TYPE_COMPLAINT = 'complaint';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'type',
        'details',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $ticket->ticket_number = self::generateTicketNumber();
        });
    }

    public static function generateTicketNumber()
    {
        do {
            $number = 'TKT-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }

    public function latestReply()
    {
        return $this->hasOne(TicketReply::class)->latestOfMany();
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_CLOSED => 'secondary',
            self::STATUS_ACCEPTED => 'success',
            self::STATUS_REJECTED => 'danger'
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst(str_replace('_', ' ', $this->status)) . '</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::TYPE_REFUND => 'primary',
            self::TYPE_COMPLAINT => 'warning'
        ];

        $class = $badges[$this->type] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($this->type) . '</span>';
    }


    public function scopeMine($query)
    {
        return $query->where('user_id', auth('patient')->id());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', [self::STATUS_CLOSED, self::STATUS_ACCEPTED, self::STATUS_REJECTED]);
    }

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('ticket_attachments')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'mime_type' => $media->mime_type,
            ];
        })->toArray();
    }

    public function isOpen()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    public function isClosed()
    {
        return in_array($this->status, [self::STATUS_CLOSED, self::STATUS_ACCEPTED, self::STATUS_REJECTED]);
    }
}
