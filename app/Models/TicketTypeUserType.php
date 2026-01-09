<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTypeUserType extends Model
{
    protected $table = 'ticket_type_user_types';

    protected $fillable = [
        'ticket_type_id',
        'user_type',
    ];

    /**
     * Get the ticket type that owns this user type mapping.
     */
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
}
