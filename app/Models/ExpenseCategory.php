<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'clinic_id',
        'notes',
    ];

    // scopes

    // for current clinic
    public function scopeForCurrentClinic($query)
    {
        return $query->where('clinic_id', auth('clinic')->user()->clinic_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
