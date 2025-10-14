<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Governorate extends Model
{
    /** @use HasFactory<\Database\Factories\GovernorateFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
