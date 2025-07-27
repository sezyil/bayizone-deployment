<?php

namespace App\Http\Resources\Client\Customer;

use App\Models\CustomerBankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerBankAccountDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        /** @var CustomerBankAccount $collection */
        $collection = $this->collection->first();
        $data = [
            'bank_name' => $collection->bank_name,
            'branch_name' => $collection->branch_name,
            'account_name' => $collection->account_name,
            'account_number' => $collection->account_number,
            'iban' => $collection->iban,
            'swift_code' => $collection->swift_code,
            'currency' => $collection->currency,
            'status' => (bool)$collection->status,
        ];
        return $data;
    }
}
