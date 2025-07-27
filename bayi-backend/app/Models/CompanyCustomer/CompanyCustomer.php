<?php

namespace App\Models\CompanyCustomer;

use App\Models\Customer;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Countries;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCustomer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'authorized_name',
        'tax_identity_no',
        'tax_office',
        'company_name',
        'phone',
        'fax',
        'email',
        'address',
        'city_id',
        'state_id',
        'country_id',
        'postcode',
        'type',
        'status',
        'group',
        'language',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    //creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->code = $model->generateCode();
        });
        static::created(function ($model) {
            $model->createBalance();
        });
    }

    //generate code
    public function generateCode()
    {
        //9 digit random number
        $generate = function () {
            return rand(100000000, 999999999);
        };

        $code = $generate();
        //check if code is unique
        while (CompanyCustomer::where('code', $code)->exists()) {
            $code = $generate();
        }

        return $code;
    }


    //customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    //warehouse
    public function warehouses()
    {
        return $this->hasMany(CompanyCustomerWarehouse::class, 'company_customer_id', 'id');
    }

    //customer transactions
    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class, 'company_customer_id', 'id');
    }

    //user
    public function users()
    {
        return $this->hasMany(CompanyCustomerUser::class, 'company_customer_id', 'id');
    }

    //country
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    //order
    public function orders()
    {
        return $this->hasMany(CustomerOrder::class, 'company_customer_id', 'id');
    }

    //offer
    public function offers()
    {
        return $this->hasMany(CustomerOffer::class, 'company_customer_id', 'id');
    }
    //balances
    public function balances()
    {
        return $this->hasMany(CompanyCustomerBalance::class, 'company_customer_id', 'id');
    }



    public function createBalance()
    {
        $currencies = Currency::all()->pluck('code');
        foreach ($currencies as $currency) {
            $balance = $this->balances()->create([
                'currency' => $currency,
            ]);

            $balance->syncAll();
        }
    }

    //clear balance
    public function clearBalance()
    {
        $balances = $this->balances;
        foreach ($balances as $balance) {
            $balance->clear();
        }
    }

    //calculate balance
    public function calculateBalance()
    {
        $balances = $this->balances;
        foreach ($balances as $balance) {
            $balance->syncAll();
        }
    }
}
