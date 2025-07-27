<?php

namespace App\View\Components\Proforma;

use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOffer;
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
    public function __construct(CustomerOffer $data)
    {
        /** @var CompanyCustomer $company_customer */
        $company_customer = $data->company_customer;
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
        return view('components.proforma.customer-detail');
    }
}
