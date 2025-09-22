<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleApprovement extends Model
{
    //
    protected $fillable = [
        'module_type',
        'module_id',
        'action_by',
        'action',
        'notes',
    ];

    public function module()
    {
        return $this->morphTo();
    }
}
