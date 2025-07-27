<?php

namespace App\View\Components\Customerorder;

use App\Enums\CustomerOrderStatusEnum;
use App\Models\Customer\CustomerOrder;
use App\Models\System\Currency;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InvoiceDetail extends Component
{
    //order no
    public $order_no;
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

    /**
     * Create a new component instance.
     */
    public function __construct(CustomerOrder $data,$orderno)
    {
        $this->currency = $data->currency;
        Carbon::setLocale(app()->getLocale());
        $this->currency_name = Currency::getName($data->currency);
        $this->offer_date = Carbon::parse($data->order_date)->isoFormat('LL');
        $this->status = CustomerOrderStatusEnum::description($data->status);
        $this->order_no = $orderno;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.customerorder.invoice-detail');
    }

    //translate status
    public function translateStatus($status)
    {
        return __('offer-status.' . $status);
    }
}
