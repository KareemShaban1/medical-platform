<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicInventoryMovement extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'clinic_inventory_id',
        'quantity',
        'type',
        'movement_date',
        'notes',
    ];

    public function clinicInventory()
    {
        return $this->belongsTo(ClinicInventory::class);
    }
}