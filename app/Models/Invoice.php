<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'appointment_id',
        'patient_id',
        'doctor_profile_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function recalcTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        $discount = $this->discount ?? 0;
        $tax = $this->tax ?? 0;
        $total = max(0, ($subtotal + $tax) - $discount);
        $this->update([
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }
}

