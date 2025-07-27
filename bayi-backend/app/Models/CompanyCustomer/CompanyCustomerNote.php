<?php

namespace App\Models\CompanyCustomer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCustomerNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'company_customer_id',
        'note',
    ];

    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class, 'company_customer_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
