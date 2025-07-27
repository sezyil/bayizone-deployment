<?php

namespace App\View\Components\Customerorder;

use App\Models\Customer\CustomerOrder;
use App\Models\System\Cities;
use App\Models\System\Countries;
use App\Models\System\States;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PaymentDetail extends Component
{
    //billing address
    public $billing_address;
    //billing city id
    public $billing_city_id;
    //billing state id
    public $billing_state_id;
    //billing country id
    public $billing_country_id;
    //billing full location
    public $billing_full_location = null;

    public $delivery_type;
    //payment type
    public $payment_type;
    //incoterms
    public $incoterms;


    /**
     * Create a new component instance.
     */
    public function __construct(CustomerOrder $data)
    {
        $this->billing_address = $data->billing_address;
        $this->billing_city_id = $data->billing_city_id;
        $this->billing_state_id = $data->billing_state_id;
        $this->billing_country_id  = $data->billing_country_id;
        $this->billing_full_location = $this->generateFullLocation();


        $this->delivery_type = $data->delivery_type;
        $this->payment_type = $data->payment_type;
        $this->incoterms = $data->incoterms;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.customerorder.payment-detail');
    }

    //generate full location
    public function generateFullLocation()
    {
        $location = [];
        $fullLocation= null;
        if ($this->billing_country_id) {
            $location[] = Countries::find($this->billing_country_id)->translateName(true);
        }
        if ($this->billing_state_id) {
            $location[] = States::find($this->billing_state_id)->name;
        }
        if ($this->billing_city_id) {
            $location[] = Cities::find($this->billing_city_id)->name;
        }

        if (count($location) > 0) {
            $fullLocation = implode('/', $location);
        }

        return $fullLocation;
    }
}
