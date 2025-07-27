<?php

namespace App\Http\Resources\Client\Customer;

use App\Models\CompanyCustomer\CompanyCustomerWarehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCustomerWarehouseListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        /** @var CompanyCustomerWarehouse[] $collection */
        $collection = $this->collection;

        foreach ($collection as $item) {
            /** @var CompanyCustomerWarehouse $item */
            $data[] = [
                'id' => $item->id,
                'company_customer_id' => $item->company_customer_id,
                'customer_name' => $item->companyCustomer->company_name,
                'name' => $item->name,
                'address' => $item->address,
                'phone' => $item->phone,
                'email' => $item->email,
                'contact_person' => $item->contact_person,
                'country' => $item->country->translateName(),
                'country_id' => $item->country_id,
                'state' => $item->state?->name ?? '---',
                'state_id' => $item->state_id,
                'city' => $item->city?->name ?? '---',
                'city_id' => $item->city_id,
                'zip_code'  => $item->zip_code,
            ];
        }

        return $data;
    }
}
