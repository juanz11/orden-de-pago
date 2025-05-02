<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Supplier;
use App\Models\OrderItem;
use App\Models\OrderApproval;

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
        'total',
        'observations',
        'exchange_rate'
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

    public function approvals()
    {
        return $this->hasMany(OrderApproval::class);
    }

    public function getApprovalCountAttribute()
    {
        return $this->approvals()->where('status', 'aprobado')->count();
    }

    public function hasUserApproved($userId)
    {
        return $this->approvals()->where('admin_id', $userId)->exists();
    }

    public function getApprovalProgressAttribute()
    {
        return $this->approval_count . '/3';
    }

    public function isFullyApproved()
    {
        return $this->approval_count >= 3;
    }
}
