<?php

namespace App\Http\Resources\Client\CompanyCustomer;

use App\Models\CompanyCustomer\CompanyCustomerWarehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCustomerWarehouseDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CompanyCustomerWarehouse */
        $collection = $this->collection->first();
        $data = [
            "id" => $collection->id,
            "company_customer_id" => $collection->company_customer_id,
            "customer_name" => $collection->companyCustomer->company_name,
            "name"  => $collection->name,
            "address"   => $collection->address,
            "phone" => $collection->phone,
            "email" => $collection->email,
            "contact_person"    => $collection->contact_person,
            "country_id"   => $collection->country_id,
            "city_id"  => $collection->city_id,
            "state_id" => $collection->state_id,
            "contact_person_phone"  => $collection->contact_person_phone,
            "contact_person_email"  => $collection->contact_person_email,
            "zip_code"  => $collection->zip_code,
        ];
        return $data;
    }
}
