<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\SupplierUser;

class Supplier extends Model implements HasMedia
{
    use InteractsWithMedia;

    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_allowed',
        'status',
    ];

    public $appends = ['images'];

    
    public function getImagesAttribute()
    {
        return $this->getMedia('supplier_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    public function supplierUsers()
    {
        return $this->hasMany(SupplierUser::class);
    }
}