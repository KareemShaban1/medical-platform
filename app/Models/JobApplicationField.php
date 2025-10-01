<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicationField extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationFieldsFactory> */
    use HasFactory;

    protected $fillable = [
        'job_id',
        'field_name',
        'field_label',
        'field_type',
        'required',
        'options',
        'order',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
