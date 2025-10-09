<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'clinic_user_id',
        'clinic_id',
        'order_id',
        // 'status',
        'payment_method',
        // 'payment_status',
        'subtotal',
        'shipping',
        'tax',
        'discount',
        'total',
    ];

    protected $casts = [
        'payment_method' => 'integer',
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the cart associated with the checkout.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the clinic user that owns the checkout.
     */
    public function clinicUser(): BelongsTo
    {
        return $this->belongsTo(ClinicUser::class, 'clinic_user_id');
    }

    /**
     * Get the clinic that owns the checkout.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the order associated with the checkout.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
