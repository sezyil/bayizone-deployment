<?php

namespace App\Http\Resources\Client\Company;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyDetailCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        /** @var Customer $this */

        $data = [
            "authorized_person" => $this->authorized_person ?? "",
            "firm_name" => $this->firm_name ?? "",
            "tax_no" => $this->tax_no ?? "",
            "tax_administration" => $this->tax_administration ?? "",
            "address" => $this->address ?? "",
            "postcode" => $this->postcode ?? "",
            "country_id" => $this->country_id ?? 0,
            "state_id" => $this->state_id ?? 0,
            "city_id" => $this->city_id ?? 0,
            "email" => $this->email ?? "",
            "phone" => $this->phone ?? "",
            "fax" => $this->fax ?? "",
        ];

        return $data;
    }
}
