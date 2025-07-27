<?php

namespace App\Models;

use App\Models\Customer\CustomerOrderLine;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerShipmentLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_shipment_id',
        'customer_order_line_id',
        'line_no',
        'quantity',
        'unit_volume',
        'unit_package',
        'total_volume',
        'total_package',
        'weight',
        'total_weight',
        'note',
    ];

    public function shipment()
    {
        return $this->belongsTo(CustomerShipment::class, 'customer_shipment_id');
    }

    public function orderLine()
    {
        return $this->belongsTo(CustomerOrderLine::class, 'customer_order_line_id');
    }
}
