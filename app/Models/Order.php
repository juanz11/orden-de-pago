<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Supplier;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'unit_price',
        'quantity',
        'total_amount',
        'status',
        'admin_comments',
        'supplier_id',
        'other_supplier',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->total_amount = $order->unit_price * $order->quantity;
        });

        static::updating(function ($order) {
            if ($order->isDirty(['unit_price', 'quantity'])) {
                $order->total_amount = $order->unit_price * $order->quantity;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isDeclined()
    {
        return $this->status === 'declined';
    }
}
