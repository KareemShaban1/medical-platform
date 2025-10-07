<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class ClinicInventory extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'item_name',
        'description',
        'quantity',
        'unit',
    ];
        
    public $appends = ['main_image', 'images'];

    public function getMainImageAttribute()
    {
        return $this->getMedia('main_image')->first()?->getUrl() ?? null;
    }

    public function getImagesAttribute()
    {
        return $this->getMedia('clinic_inventory_images')->map(function ($media) {
            return $media?->getUrl() ?? null;
        })->toArray();
    }
    

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function movements()
    {
        return $this->hasMany(ClinicInventoryMovement::class);
    }

}