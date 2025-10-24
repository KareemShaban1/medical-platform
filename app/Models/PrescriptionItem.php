<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrescriptionItem extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionItemFactory> */
    use HasFactory;
    protected $fillable = [
        'prescription_id',
        'drug_name',
        'dose',
        'frequency',
        'duration',
        'notes',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
