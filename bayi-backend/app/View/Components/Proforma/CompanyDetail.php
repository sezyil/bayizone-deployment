<?php

namespace App\View\Components\Proforma;

use App\Models\Customer;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CompanyDetail extends Component
{
    //company name
    public $name;
    //company address
    public $address;
    //company phone
    public $phone;
    //company email
    public $email;
    //company contact
    public $contact;


    /**
     * Create a new component instance.
     */
    public function __construct(Customer $data)
    {
        $this->name = $data->firm_name;
        $this->address = $data->address;
        $this->phone = $data->phone;
        $this->email = $data->email;
        $this->contact = $data->authorized_person;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.proforma.company-detail');
    }
}
