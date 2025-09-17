<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name_ar',
        'name_en',
        'slug_ar',
        'slug_en',
        'status',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}