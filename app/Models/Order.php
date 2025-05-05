<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Supplier;
use App\Models\OrderItem;
use App\Models\OrderApproval;
use App\Models\OrderPayment;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pendiente';
    const STATUS_APPROVED = 'aprobado';
    const STATUS_REJECTED = 'rechazado';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'other_supplier',
        'total',
        'observations',
        'exchange_rate',
        'status'
    ];

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

    public function approvals()
    {
        return $this->hasMany(OrderApproval::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function getApprovalCountAttribute()
    {
        return $this->approvals()->where('status', 'aprobado')->count();
    }

    public function getApprovalProgressAttribute()
    {
        return $this->approval_count . '/3';
    }

    public function hasUserApproved($userId)
    {
        return $this->approvals()->where('user_id', $userId)->exists();
    }

    public function isFullyApproved()
    {
        return $this->approval_count >= 3;
    }

    public function getTotalPaidPercentageAttribute()
    {
        return $this->payments()->sum('percentage');
    }

    public function getRemainingPercentageAttribute()
    {
        return 100 - $this->total_paid_percentage;
    }

    public function getPaymentStatusAttribute()
    {
        $totalPercentage = $this->total_paid_percentage;
        
        if ($totalPercentage >= 100) {
            return [
                'text' => 'Pagado (100%)',
                'color' => 'green'
            ];
        } elseif ($totalPercentage > 0) {
            return [
                'text' => 'Pago Parcial (' . number_format($totalPercentage, 1) . '%)',
                'color' => 'yellow'
            ];
        } else {
            return [
                'text' => 'Sin Pagos',
                'color' => 'red'
            ];
        }
    }
}
