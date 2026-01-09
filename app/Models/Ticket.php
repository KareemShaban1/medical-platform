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

    protected $fillable = [
        'ticket_number',
        'ticketable_type',
        'ticketable_id',
        'ticket_type_id',
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

    /**
     * Get the owning ticketable model (User, ClinicUser, SupplierUser, AffiliateUser).
     */
    public function ticketable()
    {
        return $this->morphTo();
    }

    /**
     * Get the ticket type.
     */
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * Alias for backward compatibility - get the user if ticketable is User.
     */
    public function user()
    {
        // Return the ticketable if it's a User, otherwise return null relation
        if ($this->ticketable_type === User::class) {
            return $this->ticketable();
        }

        // Return empty belongsTo for backward compatibility
        return $this->belongsTo(User::class, 'ticketable_id');
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
        if ($this->ticketType) {
            return $this->ticketType->badge;
        }

        return '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get the user type label for display.
     */
    public function getUserTypeLabelAttribute(): string
    {
        return match ($this->ticketable_type) {
            User::class => 'Patient',
            ClinicUser::class => 'Clinic User',
            SupplierUser::class => 'Supplier User',
            AffiliateUser::class => 'Affiliate User',
            default => 'Unknown',
        };
    }

    /**
     * Get the user type badge for display.
     */
    public function getUserTypeBadgeAttribute(): string
    {
        $colors = [
            User::class => 'primary',
            ClinicUser::class => 'success',
            SupplierUser::class => 'info',
            AffiliateUser::class => 'warning',
        ];

        $color = $colors[$this->ticketable_type] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . $this->user_type_label . '</span>';
    }

    /**
     * Scope for tickets belonging to the currently authenticated user.
     * Works with all auth guards.
     */
    public function scopeMine($query)
    {
        $user = null;
        $guards = ['patient', 'clinic', 'supplier', 'affiliate'];

        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                $user = auth($guard)->user();
                break;
            }
        }

        if ($user) {
            return $query->where('ticketable_type', get_class($user))
                ->where('ticketable_id', $user->id);
        }

        // No authenticated user, return empty result
        return $query->whereRaw('1 = 0');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('ticket_type_id', $typeId);
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
