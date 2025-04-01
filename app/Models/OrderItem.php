<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'description',
        'unit_price',
        'quantity',
        'total'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total = $item->unit_price * $item->quantity;
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
