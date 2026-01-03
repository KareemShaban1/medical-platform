<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SupplierPayoutRequest extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'supplier_id',
        'amount',
        'payout_method',
        'payout_details',
        'supplier_note',
        'admin_note',
        'status',
        'paid_by_admin_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    /**
     * Register media collections for proof images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payout_proofs');
    }

    /**
     * Get proof images URLs
     */
    public function getProofImagesAttribute(): array
    {
        return $this->getMedia('payout_proofs')->map(fn($media) => $media->getUrl())->toArray();
    }

    /**
     * Relationship to supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship to admin who marked as paid
     */
    public function paidByAdmin()
    {
        return $this->belongsTo(Admin::class, 'paid_by_admin_id');
    }

    /**
     * Relationship to order_suppliers through pivot
     */
    public function orderSuppliers()
    {
        return $this->belongsToMany(OrderSupplier::class, 'supplier_payout_request_orders')
            ->withPivot('amount')
            ->withTimestamps();
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for paid requests
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_APPROVED => 'bg-info',
            self::STATUS_PAID => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PAID => __('Paid'),
            self::STATUS_REJECTED => __('Rejected'),
            default => __('Unknown'),
        };
    }

    /**
     * Check if can be marked as paid
     */
    public function canBeMarkedAsPaid(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Check if can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get minimum payout amount from config
     */
    public static function getMinimumPayoutAmount(): float
    {
        return (float) env('SUPPLIER_MIN_PAYOUT_AMOUNT', 100);
    }

    /**
     * Get payout cooldown in weeks from config
     */
    public static function getPayoutCooldownWeeks(): int
    {
        return (int) env('SUPPLIER_PAYOUT_COOLDOWN_WEEKS', 2);
    }

    /**
     * Check if supplier can request payout (cooldown check)
     */
    public static function canSupplierRequestPayout(int $supplierId): array
    {
        $cooldownWeeks = self::getPayoutCooldownWeeks();
        $cooldownDate = now()->subWeeks($cooldownWeeks);

        $lastRequest = self::where('supplier_id', $supplierId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_PAID])
            ->latest()
            ->first();

        if (!$lastRequest) {
            return ['can_request' => true, 'next_request_date' => null, 'days_remaining' => 0];
        }

        $nextRequestDate = $lastRequest->created_at->addWeeks($cooldownWeeks);

        if (now()->gte($nextRequestDate)) {
            return ['can_request' => true, 'next_request_date' => null, 'days_remaining' => 0];
        }

        return [
            'can_request' => false,
            'next_request_date' => $nextRequestDate,
            'days_remaining' => now()->diff($nextRequestDate)->days,
            'hours_remaining' => now()->diff($nextRequestDate)->h,
        ];
    }
}
