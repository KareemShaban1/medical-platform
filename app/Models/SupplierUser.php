<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SupplierUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\SupplierUserFactory> */
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'password',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}