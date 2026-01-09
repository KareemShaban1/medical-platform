<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'badge_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all tickets of this type.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the allowed user types for this ticket type.
     */
    public function allowedUserTypes()
    {
        return $this->hasMany(TicketTypeUserType::class);
    }

    /**
     * Scope to get only active ticket types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get ticket types available for a specific user type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userType One of: 'user', 'clinic_user', 'supplier_user', 'affiliate_user'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserType($query, string $userType)
    {
        return $query->whereHas('allowedUserTypes', function ($q) use ($userType) {
            $q->where('user_type', $userType);
        });
    }

    /**
     * Check if this ticket type is available for a given user type.
     *
     * @param string $userType
     * @return bool
     */
    public function isAvailableFor(string $userType): bool
    {
        return $this->allowedUserTypes()->where('user_type', $userType)->exists();
    }

    /**
     * Sync allowed user types for this ticket type.
     *
     * @param array $userTypes
     * @return void
     */
    public function syncUserTypes(array $userTypes): void
    {
        // Delete existing
        $this->allowedUserTypes()->delete();

        // Insert new
        foreach ($userTypes as $userType) {
            $this->allowedUserTypes()->create([
                'user_type' => $userType,
            ]);
        }
    }

    /**
     * Get the user types as an array.
     *
     * @return array
     */
    public function getUserTypesArrayAttribute(): array
    {
        return $this->allowedUserTypes->pluck('user_type')->toArray();
    }

    /**
     * Get the badge HTML attribute.
     *
     * @return string
     */
    public function getBadgeAttribute(): string
    {
        return '<span class="badge bg-' . $this->badge_color . '">' . e($this->name) . '</span>';
    }

    /**
     * Get all available user types.
     *
     * @return array
     */
    public static function availableUserTypes(): array
    {
        return [
            'user' => 'Patient (User)',
            'clinic_user' => 'Clinic User',
            'supplier_user' => 'Supplier User',
            'affiliate_user' => 'Affiliate User',
        ];
    }
}
