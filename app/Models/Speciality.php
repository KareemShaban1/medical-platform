<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speciality extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'specialties';

    protected $fillable = [
        'name_en',
        'name_ar',
    ];

    public function doctorProfiles()
    {
        return $this->hasMany(DoctorProfile::class, 'speciality_id');
    }
}

