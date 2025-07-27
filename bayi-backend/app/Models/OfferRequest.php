<?php

namespace App\Models;

use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferRequest extends Model
{
    const PREFIX = 'TKF';
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'global_note',
        'status',
        'customer_offer_id',
        'from_store',
        'request_no',
        'currency',
    ];

    protected $casts = [
        'from_store' => 'boolean',
    ];

    //boot creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            //order format is TKF-0000001
            //get last 'request_no'
            $model->request_no = $model->createRequestNo();
        });
    }


    public function createRequestNo()
    {
        $lastOrder = OfferRequest::whereCustomerId($this->customer_id)->orderBy('created_at', 'desc')->first();
        if ($lastOrder) {
            $lastNo = $lastOrder->request_no;
            $lastNo = explode('-', $lastNo);
            $lastNo = isset($lastNo[1]) ? $lastNo[1] : 1;
            $lastNo = (int)$lastNo + 1;
            $lastNo = str_pad($lastNo, 6, '0', STR_PAD_LEFT);
            while (OfferRequest::whereRequestNo(self::PREFIX . '-' . $lastNo)->exists()) {
                $lastNo = (int)$lastNo + 1;
                $lastNo = str_pad($lastNo, 6, '0', STR_PAD_LEFT);
            }
            return self::PREFIX . '-' . $lastNo;
        } else {
            return self::PREFIX . '-000001';
        }
    }

    //currency
    public function currencyRelation()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    //customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    //company_customer
    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class, 'company_customer_id', 'id');
    }

    public function lines()
    {
        return $this->hasMany(OfferRequestLine::class, 'offer_request_id', 'id');
    }


}
