<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (InvoiceItem $item) {
            $qty = (int)($item->quantity ?? 1);
            $item->quantity = $qty;
            $item->total = ($item->unit_price ?? 0) * max(1, $qty);
        });

        static::saved(function (InvoiceItem $item) {
            $item->invoice?->recalcTotals();
        });

        static::deleted(function (InvoiceItem $item) {
            $item->invoice?->recalcTotals();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

