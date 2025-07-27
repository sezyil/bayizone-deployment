<?php

namespace App\Models\Customer;

use App\Enums\CustomerOrderStatusEnum;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class CustomerOffer extends Model
{
    use HasFactory, HasUuids;
    const PREFIX = 'PRO';

    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'total_price',
        'total_tax',
        'total_discount',
        'grand_total',
        'offer_date',
        'offer_due_date',
        'offer_no',
        'currency',
        'note',
        'billing_address',
        'billing_city_id',
        'billing_state_id',
        'billing_country_id',
        'billing_zip_code',
        'payment_bank_name',
        'payment_branch_name',
        'payment_account_name',
        'payment_account_number',
        'payment_iban',
        'payment_swift_code',
        'contact_person',
        'contact_email',
        'contact_phone',
        'whatsapp_notification_date',
        'mail_notification_date',
        'password',
        'is_request',
        'international_order',
        'visible_columns',
        'customer_order_id',
        'status',
        'total_volume',
        'total_package',
        'delivery_type',
        'payment_type',
        'incoterms'
    ];

    protected $casts = [
        'international_order' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->offer_no = $model->createOfferNo();
        });
    }

    public function createOfferNo()
    {
        $lastOffer = CustomerOffer::whereCustomerId($this->customer_id)->orderBy('created_at', 'desc')->first();
        if ($lastOffer) {
            /** @var CustomerOffer $lastOffer */
            $lastOfferNo = $lastOffer->offer_no;
            $lastOfferNo = explode('-', $lastOfferNo);
            $lastOfferNo = isset($lastOfferNo[1]) ? $lastOfferNo[1] : 1;
            $lastOfferNo = (int)$lastOfferNo + 1;
            $lastOfferNo = str_pad($lastOfferNo, 6, '0', STR_PAD_LEFT);
            while (CustomerOrder::whereOrderNo(self::PREFIX . '-' . $lastOfferNo)->exists()) {
                $lastOfferNo = (int)$lastOfferNo + 1;
                $lastOfferNo = str_pad($lastOfferNo, 6, '0', STR_PAD_LEFT);
            }
            return self::PREFIX . '-' . $lastOfferNo;
        } else {
            return self::PREFIX . '-000001';
        }
    }

    //customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    //company customer
    public function company_customer()
    {
        return $this->belongsTo(CompanyCustomer::class);
    }

    //order
    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    //lines
    public function lines()
    {
        return $this->hasMany(CustomerOfferLine::class, 'customer_offer_id');
    }

    //currency
    public function getCurrency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function saveOffer($customer_id, $company_customer_id, $data, $offer_id = null)
    {
        if (!$offer_id) {
            $model = new CustomerOffer();
            $model->customer_id = $customer_id;
            $model->password = $this->createPassword();
        } else {
            $model = CustomerOffer::where('id', $offer_id)->first();
        }
        $model->company_customer_id = $company_customer_id;
        $model->total_price = 0;
        $model->total_tax = 0;
        $model->total_discount = 0;
        $model->grand_total = 0;
        $model->offer_date = $data['offer_date'];
        $model->offer_due_date = $data['offer_due_date'];
        $model->currency = $data['currency'];
        $model->note = $data['note'];
        $model->billing_address = $data['billing_address'];
        $model->billing_city_id = $data['billing_city_id'] == 0 ? null : $data['billing_city_id'];
        $model->international_order = (bool)$data['is_international'];
        $model->billing_country_id = $data['billing_country_id'];
        $model->billing_state_id = $data['billing_state_id'] == 0 ? null : $data['billing_state_id'];
        $model->billing_zip_code = $data['billing_zip_code'];
        $model->contact_person = $data['contact_person'] ?? null;
        $model->contact_email = $data['contact_email'] ?? null;
        $model->contact_phone = $data['contact_phone'] ?? null;
        $model->incoterms = $data['incoterms'] ?? null;
        /* payment_account_name */
        $model->payment_account_name = $data['payment_account_name'];
        /* payment_account_number */
        $model->payment_account_number = $data['payment_account_number'];
        /* payment_bank_name */
        $model->payment_bank_name = $data['payment_bank_name'];
        /* payment_branch_name */
        $model->payment_branch_name = $data['payment_branch_name'];
        /* payment_iban */
        $model->payment_iban = $data['payment_iban'];
        /* payment_swift_code */
        $model->payment_swift_code = $data['payment_swift_code'] ?? null;
        $model->delivery_type = $data['delivery_type'] ?? null;
        $model->payment_type = $data['payment_type'] ?? null;
        if ($model->save()) {
            return $model->id;
        } else return null;
    }

    private function createPassword()
    {
        $isUnique = false;
        $password = '';
        while (!$isUnique) {
            $password = Str::random(8);
            $isUnique = CustomerOffer::where('password', $password)->count() == 0;
        }
        return $password;
    }

    public function generatePreviewUri()
    {
        return route('proforma-invoice.approval', ['proformaId' => $this->id]) . '?pass=' . $this->password;
    }


    /**
     * Convert to order
     *
     * @return int|null $order_id | null
     */
    public function convertToOrder()
    {
        if ($this->lines->count() == 0) {
            return null;
        }
        $order = new CustomerOrder();
        $order->customer_id = $this->customer_id;
        $order->company_customer_id = $this->company_customer_id;
        $order->currency = $this->currency;
        $order->total_price = $this->total_price;
        $order->total_tax = $this->total_tax;
        $order->total_discount = $this->total_discount;
        $order->grand_total = $this->grand_total;
        $order->billing_address = $this->billing_address;
        $order->billing_city_id = $this->billing_city_id;
        $order->billing_state_id = $this->billing_state_id;
        $order->currency = $this->currency;
        $order->billing_country_id = $this->billing_country_id;
        $order->delivery_type = $this->delivery_type;
        $order->payment_type = $this->payment_type;
        $order->status = CustomerOrderStatusEnum::DRAFT->value;
        $order->order_date = now();
        $order->note = $this->note;
        $order->save();

        foreach ($this->lines as $line) {
            /** @var CustomerOfferLine $line */

            /** @var CustomerOrderLine $lineModel */
            $lineModel = $order->lines()->create([
                'product_id' => $line->product_id,
                'product_name' => $line->product_name,
                'product_code' => $line->product_code,
                'product_unit' => $line->product?->unit?->description?->name ?? $line->product_unit,
                'product_image_url' => $line->product_image_url,
                'unit_id' => $line->unit_id,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'tax_rate' => $line->tax_rate,
                'unit_discount_price' => $line->unit_discount_price,
                'unit_discount_rate' => $line->unit_discount_rate,
                'total_discount_price' => $line->total_discount_price,
                'unit_volume' => $line->unit_volume,
                'unit_package' => $line->unit_package,
                'total_volume' => $line->total_volume,
                'total_package' => $line->total_package,
                'total_price' =>  $line->total_price,
                'grand_total' => $line->grand_total,
                'note' => $line->note,
            ]);

            //variants
            foreach ($line->variants as $variant) {
                $lineModel->variants()->create([
                    'type' => $variant->type,
                    'value' => $variant->value,
                    'product_variant_id' => $variant->product_variant_id,
                    'product_variant_value_id' => $variant->product_variant_value_id,
                ]);
            }
        }

        $order->histories()->create([
            'status_code' => CustomerOrderStatusEnum::DRAFT->value,
            'note' => 'Proforma siparişten oluşturuldu. Proforma no: ' . $this->offer_no . ' Tarih: ' . now()->format('d.m.Y H:i:s'),
        ]);

        if ($order->id) {
            $this->customer_order_id = $order->id;
            $this->save();
        }

        return $order?->id;
    }
}
