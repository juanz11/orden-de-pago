<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;

/**
 * Configurando el modelo OrderPayment
 */
class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'related_order_id',
        'percentage',
        'amount',
        'status',
        'comments'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function relatedOrder()
    {
        return $this->belongsTo(Order::class, 'related_order_id');
    }
}
