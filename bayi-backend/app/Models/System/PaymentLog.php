<?php

namespace App\Models\System;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'payment_status',
        'payment_error_code',
        'payment_error_message',
        'payment_token',
        'payment_response',
        'payment_request',
    ];

    protected $casts = [
        'payment_response' => 'array',
        'payment_request' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
