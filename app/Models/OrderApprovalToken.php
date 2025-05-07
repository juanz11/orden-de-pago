<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderApprovalToken extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return !$this->used_at && $this->expires_at->isFuture();
    }
}
