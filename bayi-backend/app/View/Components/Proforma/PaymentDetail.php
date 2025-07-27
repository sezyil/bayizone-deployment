<?php

namespace App\View\Components\Proforma;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PaymentDetail extends Component
{
    //bank name
    public $bank_name;
    //branch name
    public $branch_name;
    //account name
    public $account_name;
    //account number
    public $account_number;
    //iban
    public $iban;
    //swift code
    public $swift_code;
    //delivery type
    public $delivery_type;
    //payment type
    public $payment_type;
    //incoterms
    public $incoterms;

    /**
     * Create a new component instance.
     */
    public function __construct($data)
    {
        $this->bank_name = $data->payment_bank_name;
        $this->branch_name = $data->payment_branch_name;
        $this->account_name = $data->payment_account_name;
        $this->account_number = $data->payment_account_number;
        $this->iban = $data->payment_iban;
        $this->swift_code = $data->payment_swift_code;
        $this->delivery_type = $data->delivery_type;
        $this->payment_type = $data->payment_type;
        $this->incoterms = $data->incoterms;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.proforma.payment-detail');
    }
}
