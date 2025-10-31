<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'link_url',
        'start_at',
        'end_at',
        'target_clinics_all',
        'target_suppliers_all',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'target_clinics_all' => 'boolean',
        'target_suppliers_all' => 'boolean',
        'status' => 'boolean',
    ];

    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'announcement_clinic');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'announcement_supplier');
    }

    public function dismissals()
    {
        return $this->hasMany(AnnouncementDismissal::class);
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', true)
            ->where(function($q) use ($now){
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function($q) use ($now){
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }
}

