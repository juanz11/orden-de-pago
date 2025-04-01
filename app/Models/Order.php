<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Supplier;
use App\Models\OrderItem;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pendiente';
    const STATUS_APPROVED = 'aprobado';
    const STATUS_DECLINED = 'rechazado';

    protected $fillable = [
        'description',
        'unit_price',
        'quantity',
        'total_amount',
        'status',
        'admin_comments',
        'supplier_id',
        'other_supplier',
        'user_id',
        'total'
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

        static::saving(function ($order) {
            if ($order->items()->exists()) {
                $order->total = $order->items()->sum('total');
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

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDeclined()
    {
        return $this->status === self::STATUS_DECLINED;
    }
}
