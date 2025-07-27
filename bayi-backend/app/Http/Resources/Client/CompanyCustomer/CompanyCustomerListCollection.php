<?php

namespace App\Http\Resources\Client\CompanyCustomer;

use App\Models\CompanyCustomer\CompanyCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCustomerListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        $collection = $this->collection;

        foreach ($collection as $item) {
            /** @var CompanyCustomer $item */

            $outputName = "";
            // 1: tüzel kişi 2: gerçek kişi
            /* if ($item->type == 1) {
                $outputName = $item->company_name;
            } else {
                $outputName = $item->authorized_name;
            } */
            $outputName = $item->company_name;
            $data[] = [
                'id' => $item->id,
                'name' => $outputName,
                'phone' => $item->phone,
                'email' => $item->email,
                'group' => $item->group,
                'code' => $item->code,
                'has_user' => $item->users->count() > 0,
                'country_name' => $item->country->translateName(),
                'city'  => $item->city,
            ];
        }

        return $data;
    }
}
