<?php

namespace App\Models;

use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Enums\Shipment\CustomerShipmentStatusEnum;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerShipment extends Model
{
    use HasFactory, HasUuids;


    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'company_customer_name',
        'shipment_no',
        'container_no',
        'carrier',
        'status',
        'note',
        'shipped_at',
        'delivered_at',
        'total_price',
        'total_tax',
        'total_discount',
        'grand_total',
        'is_international',
        'currency',
        'total_volume',
        'total_package',
        'total_weight',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_international' => 'boolean',
    ];

    //oncreated
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->shipment_no = 'S-' . date('Ymd') . '-' . strtoupper(uniqid());
        });
    }

    public function getCurrency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class);
    }

    public function lines()
    {
        return $this->hasMany(CustomerShipmentLine::class, 'customer_shipment_id');
    }

    public function histories()
    {
        return $this->hasMany(CustomerShipmentHistory::class, 'customer_shipment_id');
    }

    //get Status label
    public function statusLabel()
    {
        return CustomerShipmentStatusEnum::description($this->status);
    }

    #region Helper
    public function processShipmentAction()
    {
        foreach ($this->lines as $line) {
            /** @var CustomerShipmentLine $line */
            /** @var CustomerOrderLine $orderLine */
            $orderLine = $line->orderLine;
            $orderLine->update([
                'sent_quantity' => $orderLine->sent_quantity + $line->quantity,
            ]);
        }

        $orderIds = $this->lines()->get()->pluck('orderLine.customer_order_id')->unique();
        foreach ($orderIds as $orderId) {
            /** @var CustomerOrder $order */
            $order = CustomerOrder::find($orderId);
            $order->reArrangeStatusForShipmentModule($this->shipment_no);
        }
    }

    public function processDeliveredAction()
    {
        $this->lines->each(function ($line) {
            /** @var CustomerShipmentLine $line */
            $orderLine = $line->orderLine;
            $orderLine->update([
                'delivered_quantity' => $orderLine->delivered_quantity + $line->quantity,
            ]);
        });

        $orderIds = $this->lines()->get()->pluck('orderLine.customer_order_id')->unique();
        foreach ($orderIds as $orderId) {
            /** @var CustomerOrder $order */
            $order = CustomerOrder::find($orderId);
            $order->reArrangeStatusForShipmentModule($this->shipment_no);
        }
    }
}
