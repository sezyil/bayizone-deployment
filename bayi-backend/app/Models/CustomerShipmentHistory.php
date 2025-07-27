<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerShipmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_shipment_id',
        'note',
        'status',
        'notify',
    ];

    protected $casts = [
        'notify' => 'boolean',
    ];
}
