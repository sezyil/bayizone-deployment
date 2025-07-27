<?php

namespace App\View\Components\Proforma;

use App\Models\Customer\CustomerOffer;
use App\Models\System\Currency;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InvoiceDetail extends Component
{
    //currency code
    public $currency;
    //currency name
    public $currency_name;
    //offer date
    public $offer_date;
    //offer due date
    public $offer_due_date;
    //status
    public $status;
    public $offer_no;

    /**
     * Create a new component instance.
     */
    public function __construct(CustomerOffer $data)
    {
        $this->currency = $data->currency;
        Carbon::setLocale(app()->getLocale());
        $this->currency_name = Currency::getName($data->currency);
        $this->offer_date = Carbon::parse($data->offer_date)->isoFormat('LL');
        $this->offer_due_date = Carbon::parse($data->offer_due_date)->isoFormat('LL');
        $this->status = $this->translateStatus($data->status);
        $this->offer_no = $data->offer_no;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.proforma.invoice-detail');
    }

    //translate status
    public function translateStatus($status)
    {
        return __('offer-status.' . $status);
    }
}
