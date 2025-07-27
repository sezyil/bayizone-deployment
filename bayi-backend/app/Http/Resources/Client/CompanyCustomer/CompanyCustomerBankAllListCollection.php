<?php

namespace App\Http\Resources\Client\CompanyCustomer;

use App\Models\CompanyCustomer\CompanyCustomerBankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCustomerBankAllListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CompanyCustomerBankAccount[] $collection */
        $collection = $this->collection;
        $data = [];
        foreach ($collection as $item) {
            /** @var CompanyCustomerBankAccount $item */
            $data[] = [
                "id" => $item->id,
                "company_customer_id" => $item->company_customer_id,
                "company_customer_name" => $item->parentCompanyCustomer->company_name,
                "bank_name" => $item->bank_name,
                "branch_name" => $item->branch_name,
                "account_name" => $item->account_name,
                "iban" => $item->iban,
                "status" => (bool)$item->status,
            ];
        }

        return $data;
    }
}
