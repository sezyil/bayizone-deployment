<?php

namespace App\Models\Customer;

use App\Enums\CustomerOrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'status_code',
        'note',
        'notify',
    ];

    protected $casts = [
        'notify' => 'boolean',
    ];

    //get status description
    public function getStatusText()
    {
        return CustomerOrderStatusEnum::description($this->status_code);
    }
}
