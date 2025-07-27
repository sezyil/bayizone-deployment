<?php

namespace App\Models\Customer;

use App\Enums\CustomerOrderLineStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class CustomerOrderLineHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_line_id',
        'note',
        'status',
        'notify',
    ];

    protected $casts = [
        'notify' => 'boolean',
    ];

    public function customer_order_line()
    {
        return $this->belongsTo(CustomerOrderLine::class);
    }

    public function getStatusLabel()
    {
        return $this->status ? CustomerOrderLineStatusEnum::description($this->status) : null;
    }
}
