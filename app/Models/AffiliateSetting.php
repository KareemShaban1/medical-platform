<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_discount_percent',
        'default_commission_percent',
    ];
}
