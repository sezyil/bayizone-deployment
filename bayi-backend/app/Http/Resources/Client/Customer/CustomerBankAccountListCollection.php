<?php

namespace App\Http\Resources\Client\Customer;

use App\Models\CustomerBankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerBankAccountListCollection extends ResourceCollection
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
            /** @var CustomerBankAccount $item */
            $data[] = [
                'id' => $item->id,
                'bank_name' => $item->bank_name,
                'branch_name' => $item->branch_name,
                'account_name' => $item->account_name,
                'account_number' => $item->account_number,
                'iban' => $item->iban,
                'swift_code' => $item->swift_code,
                'currency' => $item->currency,
                'currency_name' => $item->getCurrency()->first()->title,
                'status' => (bool)$item->status,
            ];
        }

        return $data;
    }
}
