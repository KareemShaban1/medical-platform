<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SalaryContract extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;


    protected $fillable = [
        'clinic_user_id',
        'salary_type',
        'base_amount',
        'percentage_rate',
        'effective_from',
        'effective_to',
        'notes',
    ];

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('salary_contract_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }
}
