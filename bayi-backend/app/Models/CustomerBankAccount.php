<?php

namespace App\Models;

use App\Models\Customer\CustomerOrder;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBankAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'bank_name',
        'branch_name',
        'account_name',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    #region relationships
    public function getCurrency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    #endregion

    #region scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    #endregion
}
