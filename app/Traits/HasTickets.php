<?php

namespace App\Traits;

use App\Models\Ticket;

trait HasTickets
{
    /**
     * Get all tickets for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tickets()
    {
        return $this->morphMany(Ticket::class, 'ticketable');
    }

    /**
     * Get the user type identifier for ticket purposes.
     *
     * @return string
     */
    public function getTicketUserType(): string
    {
        return match (static::class) {
            \App\Models\User::class => 'user',
            \App\Models\ClinicUser::class => 'clinic_user',
            \App\Models\SupplierUser::class => 'supplier_user',
            \App\Models\AffiliateUser::class => 'affiliate_user',
            default => 'user',
        };
    }

    /**
     * Get available ticket types for this user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableTicketTypes()
    {
        return \App\Models\TicketType::active()
            ->forUserType($this->getTicketUserType())
            ->get();
    }
}
