<?php

namespace App\Models\CompanyCustomer;

use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\System\Cities;
use App\Models\System\Countries;
use App\Models\System\States;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCustomerWarehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'name',
        'address',
        'phone',
        'email',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'country_id',
        'city_id',
        'state_id',
        'zip_code'
    ];

    //boot
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->city_id = $model->city_id == 0 ? null : $model->city_id;
            $model->state_id = $model->state_id == 0 ? null : $model->state_id;
        });
    }

    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class);
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }


    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }
}
