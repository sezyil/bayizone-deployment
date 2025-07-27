<?php

namespace App\Models\CompanyCustomer;

use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Currency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCustomerBalance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'currency',
        'company_customer_id',
        'balance',
        'total_debt',
        'total_credit',
        'total_unpaid_debt',
        'total_unpaid_credit',
        'total_overdue_debt',
        'total_overdue_credit',
    ];

    protected $casts = [
        'balance' => 'float',
        'total_debt' => 'float',
        'total_credit' => 'float',
        'total_unpaid_debt' => 'float',
        'total_unpaid_credit' => 'float',
        'total_overdue_debt' => 'float',
        'total_overdue_credit' => 'float',
    ];

    #region Relations
    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class);
    }

    //currency
    public function getCurrency()
    {
        return $this->belongsTo(Currency::class, 'currency', 'code');
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class, 'company_customer_id', 'company_customer_id')->isCurrency($this->currency);
    }
    #endregion

    #region Scopes
    public function scopeIsCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }
    #endregion


    #region Methods

    //clear
    public function clear()
    {
        $this->balance = 0;
        $this->total_debt = 0;
        $this->total_credit = 0;
        $this->total_unpaid_debt = 0;
        $this->total_unpaid_credit = 0;
        $this->total_overdue_debt = 0;
        $this->total_overdue_credit = 0;

        $this->save();
    }

    public function syncBalance()
    {
        /** @var CompanyCustomer $customer */
        $customer = $this->companyCustomer;
        if ($customer === null) {
            return;
        }

        /** @var CustomerTransaction $transactions */
        $transactions = $customer->transactions()->isCurrency($this->currency);


        /* $totals = $transactions->selectRaw('
            SUM(amount) as balance,
            SUM(CASE WHEN io_type = 0 THEN amount ELSE 0 END) as total_debt,
            SUM(CASE WHEN io_type = 1 THEN amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN io_type = 0 AND is_paid = 1 THEN amount ELSE 0 END) as total_paid_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 1 THEN amount ELSE 0 END) as total_paid_credit,
            SUM(CASE WHEN io_type = 0 AND is_paid = 0 THEN amount ELSE 0 END) as total_unpaid_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 0 THEN amount ELSE 0 END) as total_unpaid_credit
        ')->first(); */
        $totals = $transactions->selectRaw('
            SUM(CASE WHEN io_type = 0 THEN amount ELSE 0 END) as total_debt,
            SUM(CASE WHEN io_type = 1 THEN amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN io_type = 0 AND is_paid = 1 THEN amount ELSE 0 END) as total_paid_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 1 THEN amount ELSE 0 END) as total_paid_credit,
            SUM(CASE WHEN io_type = 0 AND is_paid = 0 THEN amount ELSE 0 END) as total_unpaid_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 0 THEN amount ELSE 0 END) as total_unpaid_credit
        ')->first();

        $this->balance = $totals->total_credit - $totals->total_debt;
        $this->total_debt = $totals->total_debt ?? 0;
        $this->total_credit = $totals->total_credit ?? 0;
        $this->total_paid_debt = $totals->total_paid_debt ?? 0;
        $this->total_paid_credit = $totals->total_paid_credit ?? 0;
        $this->total_unpaid_debt = $totals->total_unpaid_debt ?? 0;
        $this->total_unpaid_credit = $totals->total_unpaid_credit ?? 0;


        $this->save();
    }

    public function syncOverDues()
    {
        /** @var CompanyCustomer $customer */
        $customer = $this->companyCustomer;
        if ($customer === null) {
            return;
        }
        /** @var CustomerTransaction $transactions */
        $transactions = $customer?->transactions()->isCurrency($this->currency);
        if ($transactions->count() === 0) {
            return;
        }


        $totals = $transactions->selectRaw('
            SUM(CASE WHEN io_type = 0 AND is_paid = 0 AND (due_date < NOW() AND due_date IS NOT NULL) THEN amount ELSE 0 END) as total_overdue_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 0 AND (due_date < NOW() AND due_date IS NOT NULL) THEN amount ELSE 0 END) as total_overdue_credit
        ')->first();

        //get exist currency
        $this->total_overdue_debt = $totals->total_overdue_debt ?? 0;
        $this->total_overdue_credit = $totals->total_overdue_credit ?? 0;

        $this->save();
    }

    public function syncAll()
    {
        $this->syncBalance();
        $this->syncOverDues();
    }
}
