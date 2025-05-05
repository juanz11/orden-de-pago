<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'percentage',
        'amount',
        'reference_number'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
