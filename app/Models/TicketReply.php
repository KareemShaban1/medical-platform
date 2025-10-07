<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'replied_by_type',
        'replied_by_id',
        'message',
        'is_admin_reply',
        'read_at',
    ];

    protected $casts = [
        'is_admin_reply' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function repliedBy()
    {
        return $this->morphTo();
    }

    public function scopeAdminReplies($query)
    {
        return $query->where('is_admin_reply', true);
    }

    public function scopeUserReplies($query)
    {
        return $query->where('is_admin_reply', false);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function getAuthorNameAttribute()
    {
        if ($this->is_admin_reply) {
            return $this->repliedBy->name ?? 'Admin';
        }

        return $this->repliedBy->name ?? 'User';
    }

    public function getAuthorTypeAttribute()
    {
        return $this->is_admin_reply ? 'Admin' : 'User';
    }
}
