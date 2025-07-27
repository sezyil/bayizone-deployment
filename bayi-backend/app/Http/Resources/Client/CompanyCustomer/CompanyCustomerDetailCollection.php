<?php

namespace App\Http\Resources\Client\CompanyCustomer;

use App\Models\CompanyCustomer\CompanyCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCustomerDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CompanyCustomer $collection */
        $collection = $this->collection->first();

        return [
            "authorized_name" => $collection->authorized_name,
            "tax_identity_no" => $collection->tax_identity_no,
            "tax_office" => $collection->tax_office,
            "company_name" => $collection->company_name,
            "phone" => $collection->phone,
            "fax" => $collection->fax,
            "email" => $collection->email,
            "address" => $collection->address,
            "city_id" => $collection->city_id,
            "state_id" => $collection->state_id,
            "country_id" => $collection->country_id,
            "postcode" => $collection->postcode,
            "language" => $collection->language,
            "type" => $collection->type,
            "group" => $collection->group,
            "status" => (bool)$collection->status,
        ];
    }
}
