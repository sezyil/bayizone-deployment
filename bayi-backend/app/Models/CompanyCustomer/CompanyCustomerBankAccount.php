<?php

namespace App\Models\CompanyCustomer;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCustomerBankAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'bank_name',
        'branch_name',
        'account_name',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'status',
        'customer_id',
        'company_customer_id',
    ];

    public function parentCompanyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class, 'company_customer_id');
    }
}
