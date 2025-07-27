<?php

namespace App\Services\Client\Transaction;

use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Currency;
use  Illuminate\Database\Eloquent\Builder;

class TransactionSummaryService
{
    /**
     * Get summary of transactions
     *
     * @param Builder $transactions
     * @return array
     */
    private Builder $transactions;
    /**
     * @var Currency
     */
    private $currency;

    public function __construct($transactions, $currency)
    {
        $this->transactions = $transactions;
        $this->currency = Currency::whereCode($currency)->first();
    }


    public function getSummary()
    {

        $format = fn ($amount) => $this->currency->format($amount, true);

        $totals = $this->transactions->clone()->selectRaw('
            SUM(CASE WHEN io_type = 0 THEN amount ELSE 0 END) as total_debt,
            SUM(CASE WHEN io_type = 1 THEN amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN io_type = 0 AND is_paid = 1 THEN amount ELSE 0 END) as total_paid_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 1 THEN amount ELSE 0 END) as total_paid_credit,
            SUM(CASE WHEN io_type = 0 AND is_paid = 0 THEN amount ELSE 0 END) as total_unpaid_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 0 THEN amount ELSE 0 END) as total_unpaid_credit
        ')->first();



        $total_credit = $totals->total_credit ?? 0;
        $total_debt = $totals->total_debt ?? 0;
        $balance = $total_credit - $total_debt;
        $total_paid_debt = $totals->total_paid_debt ?? 0;
        $total_paid_credit = $totals->total_paid_credit ?? 0;
        $total_unpaid_debt = $totals->total_unpaid_debt ?? 0;
        $total_unpaid_credit = $totals->total_unpaid_credit ?? 0;


        $overdues = $this->transactions->clone()->selectRaw('
            SUM(CASE WHEN io_type = 0 AND is_paid = 0 AND (due_date < NOW() AND due_date IS NOT NULL) THEN amount ELSE 0 END) as total_overdue_debt,
            SUM(CASE WHEN io_type = 1 AND is_paid = 0 AND (due_date < NOW() AND due_date IS NOT NULL) THEN amount ELSE 0 END) as total_overdue_credit
        ')->first();

        //get exist currency
        $total_overdue_debt = $overdues->total_overdue_debt ?? 0;
        $total_overdue_credit = $overdues->total_overdue_credit ?? 0;


        return [
            'total_credit' => $total_credit,
            'formatted_total_credit' => $format($total_credit),
            'total_debt' => $total_debt,
            'formatted_total_debt' => $format($total_debt),
            'balance' => $balance,
            'formatted_balance' => $format($balance),
            'total_paid_debt' => $total_paid_debt,
            'formatted_total_paid_debt' => $format($total_paid_debt),
            'total_paid_credit' =>  $total_paid_credit,
            'formatted_total_paid_credit' => $format($total_paid_credit),
            'total_unpaid_debt' => $total_unpaid_debt,
            'formatted_total_unpaid_debt' => $format($total_unpaid_debt),
            'total_unpaid_credit' => $total_unpaid_credit,
            'formatted_total_unpaid_credit' => $format($total_unpaid_credit),
            'total_overdue_debt' => $total_overdue_debt,
            'formatted_total_overdue_debt' => $format($total_overdue_debt),
            'total_overdue_credit' => $total_overdue_credit,
            'formatted_total_overdue_credit' => $format($total_overdue_credit),
        ];
    }
}
