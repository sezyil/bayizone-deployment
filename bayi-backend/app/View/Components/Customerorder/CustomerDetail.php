<?php

namespace App\View\Components\Customerorder;

use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CustomerDetail extends Component
{
    //customer name
    public $name;
    //customer address
    public $address;
    //customer phone
    public $phone;
    //customer email
    public $email;
    //customer contact
    public $contact;
    /**
     * Create a new component instance.
     */
    public function __construct(CustomerOrder $data)
    {
        /** @var CompanyCustomer $company_customer */
        $company_customer = $data->companyCustomer;
        $this->name = $company_customer->company_name;
        $this->address = $company_customer->address;
        $this->phone = $company_customer->phone;
        $this->email = $company_customer->email;
        $this->contact = $company_customer->authorized_name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.customerorder.customer-detail');
    }
}
