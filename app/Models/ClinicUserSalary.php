<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ClinicUserSalary extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'clinic_user_id',
        'amount',
        'salary_frequency',
        'amount_per_salary_frequency',
        'start_date',
        'end_date',
        'paid',
        'notes',
    ];

    public $appends = ['images'];

    public function getImagesAttribute()
    {
        return $this->getMedia('clinic_user_salary_images')->map(function ($media) {
            return $media?->getUrl() ?? null;
        })->toArray();
    }
    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }
}