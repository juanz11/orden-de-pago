<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;
use App\Models\User;

class OrderApproval extends Model
{
    use HasFactory;

    protected $table = 'order_approvals';

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'token',
        'approved_at',
        'comments'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'pendiente'
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
