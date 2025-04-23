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
        'status',
        'admin_comments',
        'admin_id',
        'supplier_id',
        'other_supplier',
        'total'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
