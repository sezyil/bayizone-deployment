<?php

namespace App\Models;

use App\Enums\OrderPaymentMethods;
use App\Models\System\Currency;
use App\Models\System\PaymentLog;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'plan_id',
        'customer_id',
        'user_id',
        'order_no',
        'total',
        'is_paid',
        'payment_method',
        'payment_date',
        'ip_address',
        'is_active',
        'created_at',
        'updated_at',
        'invoice_firm_name',
        'invoice_tax_no',
        'invoice_tax_administration',
        'invoice_address',
        'invoice_country_id',
        'invoice_country',
        'invoice_state_id',
        'invoice_state',
        'invoice_city_id',
        'invoice_city',
        'invoice_postcode',
        'invoice_email',
        'invoice_phone',
        'tax_amount',
        'subtotal',
        'payment_token',
        'converted_tax_amount',
        'converted_total',
        'converted_subtotal',
        'transfer_account_name',
        'transfer_bank_name',
        'transfer_reference_no',
        'transfer_date',
        'waiting_transfer_approve',
        'currency_rate',
        'coupon_id',
        'discount_amount',
        'converted_discount_amount',
    ];

    protected $casts = [
        'id' => 'string',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'converted_tax_amount' => 'float',
        'converted_total' => 'float',
        'converted_subtotal' => 'float',
        'waiting_transfer_approve' => 'boolean',
        'currency_rate' => 'float',
    ];

    //boot creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            //order format is ORD-0000001
            //get last 'order_no'
            $no = Order::latest('order_no')->first();
            if ($no) {
                $order->order_no = 'ORD-' . str_pad((int)substr($no->order_no, -7) + 1, 7, '0', STR_PAD_LEFT);
            } else {
                $order->order_no = 'ORD-0000001';
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //lines
    public function lines()
    {
        return $this->hasMany(OrderLine::class);
    }

    //logs
    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    //coupon
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    //is bank transfer
    public function isBankTransfer()
    {
        return $this->payment_method == OrderPaymentMethods::BANK_TRANSFER->value;
    }

    //is yearly and subscription
    public function isYearlyAndSubscription()
    {
        return $this->lines->count() == 1 && $this->lines->first()->isYearlyAndSubscription();
    }

    #region Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotPaid($query)
    {
        return $query->where('is_paid', false);
    }

    #endregion





    //disable old orders
    public function disableOldOrders()
    {
        Order::whereCustomerId($this->customer_id)
            ->whereIsActive(true)
            ->whereHas('lines')
            ->where('is_paid', false)
            ->update([
                'is_active' => false,
            ]);
    }

    //approve order
    public function approve()
    {
        $this->disableOldOrders();
        $this->is_active = false;
        $this->is_paid = true;
        $this->payment_date = now();
        $this->save();
        $this->lines->each(function ($line) {
            /** @var OrderLine $line */
            $line->activateItem();
        });
    }


    public function calculatePrices()
    {
        $this->total = $this->lines->sum('total');
        $this->tax_amount = $this->lines->sum('tax_amount');
        $this->subtotal = $this->lines->sum('subtotal');
        $this->discount_amount = 0;
        $this->converted_discount_amount = 0;
        $this->discount_percentage = 0;
        $coupon = null;
        if ($this->coupon_id) {
            $coupon = Coupon::whereId($this->coupon_id)->notExpired()->active()->first();
            if ($coupon) {
                if ($coupon->isCustomerBased() && !$coupon->customers->contains($this->customer_id)) {
                    $this->coupon_id = null;
                    $coupon = null;
                } else {
                    $this->discount_amount = $coupon->calculateDiscount($this->total);
                    $this->total -= $this->discount_amount;
                    $this->converted_discount_amount = Currency::convert($this->discount_amount, Currency::CODE_USD, Currency::CODE_TL);
                    $this->discount_percentage = $coupon->percentage;
                }
            } else {
                $this->coupon_id = null;
            }
        }

        //convert currency
        $this->converted_total = Currency::convert($this->total, Currency::CODE_USD, Currency::CODE_TL);
        $this->converted_tax_amount = Currency::convert($this->tax_amount, Currency::CODE_USD, Currency::CODE_TL);
        $this->converted_subtotal = Currency::convert($this->subtotal, Currency::CODE_USD, Currency::CODE_TL);
        $this->currency_rate = Currency::where('code', Currency::CODE_USD)->first()->rate;

        $this->save();

        if ($coupon) {
            /** @var Coupon $coupon */
            $coupon->syncUseCount();
        }
    }

    //register coupon
    public function registerCoupon(Coupon|null $coupon)
    {
        $this->coupon_id = $coupon ? $coupon->id : null;
        $this->save();

        $this->calculatePrices();

        return $this;
    }
}
