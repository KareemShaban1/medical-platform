<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Expense extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    protected $fillable = [
        'clinic_id',
        'category_id',
        'amount',
        'expense_date',
        'supplier_id',
        'notes',
    ];

    // appends
    protected $appends = ['images'];


    // scopes
    public function scopeForCurrentClinic($query)
    {
        return $query->where('clinic_id', auth('clinic')->user()->clinic_id);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('expense_images')->map(function ($media) {
            return $media?->getUrl() ?? null;
        })->toArray();
    }
}