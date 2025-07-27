<?php

namespace App\Models\Customer;

use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
use App\Models\CustomerShipment;
use App\Models\CustomerShipmentLine;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class CustomerOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    const PREFIX = 'SIP';

    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'currency',
        'order_date',
        'status',
        'total_price',
        'total_tax',
        'total_discount',
        'grand_total',
        'billing_address',
        'billing_city_id',
        'billing_state_id',
        'billing_country_id',
        'note',
        'is_international',
        'total_volume',
        'total_package',
        'delivery_type',
        'payment_type',
        'incoterms',
        'managed_by_system',
    ];

    protected $casts = [
        'is_international' => 'boolean',
        'managed_by_system' => 'boolean',
        'grand_total' => 'float',
    ];

    //boot
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_no = $model->createOrderNo();
            $model->password = $model->createPassword();
        });
    }

    //create password

    public function createPassword()
    {
        $isUnique = false;
        $password = '';
        while (!$isUnique) {
            $password = Str::random(8);
            $isUnique = CustomerOrder::where('password', $password)->count() == 0;
        }
        return $password;
    }

    public function createOrderNo()
    {
        $lastOrder = CustomerOrder::whereCustomerId($this->customer_id)->orderBy('created_at', 'desc')->first();
        if ($lastOrder) {
            $lastOrderNo = $lastOrder->order_no;
            $lastOrderNo = explode('-', $lastOrderNo);
            $lastOrderNo = isset($lastOrderNo[1]) ? $lastOrderNo[1] : 1;
            $lastOrderNo = (int)$lastOrderNo + 1;
            $lastOrderNo = str_pad($lastOrderNo, 6, '0', STR_PAD_LEFT);
            while (CustomerOrder::whereOrderNo(self::PREFIX . '-' . $lastOrderNo)->exists()) {
                $lastOrderNo = (int)$lastOrderNo + 1;
                $lastOrderNo = str_pad($lastOrderNo, 6, '0', STR_PAD_LEFT);
            }
            return self::PREFIX . '-' . $lastOrderNo;
        } else {
            return self::PREFIX . '-000001';
        }
    }


    public function generatePreviewUri()
    {
        return route('customer-order.invoice', ['orderId' => $this->id]) . '?pass=' . $this->password;
    }

    #region Relations
    public function lines()
    {
        return $this->hasMany(CustomerOrderLine::class);
    }

    public function histories()
    {
        return $this->hasMany(CustomerOrderHistory::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class);
    }

    public function transaction()
    {
        return $this->hasOne(CustomerTransaction::class, 'customer_order_id');
    }

    public function getCurrency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function offer()
    {
        return $this->belongsTo(CustomerOffer::class, 'id', 'customer_order_id');
    }


    #endregion

    #region Scopes
    //not draft
    public function scopeNotDraft($query)
    {
        return $query->where('status', '!=', CustomerOrderStatusEnum::DRAFT->value);
    }
    public function scopeManagedBySystem($query)
    {
        return $query->where('managed_by_system', true);
    }
    #endregion

    #Helpers
    public function saveOrder($customer_id, $company_customer_id, $data, $order_id = null)
    {
        if (!$order_id) {
            $model = new CustomerOrder();
            $model->customer_id = $customer_id;
            $model->status = CustomerOrderStatusEnum::DRAFT->value;
        } else {
            $model = CustomerOrder::where('id', $order_id)->first();
        }
        $model->company_customer_id = $company_customer_id;
        $model->delivery_type = $data['delivery_type'];
        $model->payment_type = $data['payment_type'];
        $model->total_price = 0;
        $model->total_tax = 0;
        $model->total_discount = 0;
        $model->grand_total = 0;
        $model->order_date = $data['order_date'];
        $model->is_international = (bool)$data['is_international'] ?? false;
        $model->currency = $data['currency'];
        $model->note = $data['note'];
        $model->incoterms = $data['incoterms'] ?? null;
        $model->billing_address = $data['billing_address'];
        $model->billing_city_id = $data['billing_city_id'] == 0 ? null : $data['billing_city_id'];
        $model->billing_country_id = $data['billing_country_id'];
        $model->billing_state_id = $data['billing_state_id'] == 0 ? null : $data['billing_state_id'];
        if ($model->save()) {
            return $model->id;
        } else return null;
    }

    public function reArrangeStatusForShipmentModule($shipment_no)
    {
        $totalQuantity = $this->lines()->sum('quantity');
        $shippedQuantity = $this->lines()->sum('sent_quantity'); //sevk edilmiş miktar
        $deliveredQuantity = $this->lines()->sum('delivered_quantity'); //teslim edilmiş miktar
        $oldStatus = $this->status;
        //CustomerOrderStatusEnum::PARTIALLY_SHIPPED

        if ($deliveredQuantity == $totalQuantity) {
            $this->status = CustomerOrderStatusEnum::DELIVERED->value;
        } elseif ($shippedQuantity > 0 && $shippedQuantity < $totalQuantity) {
            $this->status = CustomerOrderStatusEnum::PARTIALLY_SHIPPED->value;
        } elseif ($shippedQuantity == $totalQuantity) {
            $this->status = CustomerOrderStatusEnum::SHIPPED->value;
        }

        if ($oldStatus != $this->status) {
            $this->save();
            $this->histories()->create([
                'status_code' => $this->status,
                'note' => 'Sevkiyat Modülü Tarafından Güncellendi. : ' . CustomerOrderStatusEnum::description($this->status) . '. Sevkiyat No: ' . $shipment_no,
            ]);
            //if delivered set status to completed
            if ($this->status == CustomerOrderStatusEnum::DELIVERED->value) {
                $this->status = CustomerOrderStatusEnum::COMPLETED->value;
                $this->save();
                $this->histories()->create([
                    'status_code' => $this->status,
                    'note' => 'Sevkiyat Modülü Tarafından Güncellendi. : ' . CustomerOrderStatusEnum::description($this->status) . '. Sevkiyat No: ' . $shipment_no,
                ]);
            }
        }

        foreach ($this->lines as $line) {
            $line->reArrangeStatusForShipmentModule();
        }
    }

    #endregion

    #region Accessors

    #endregion
}
