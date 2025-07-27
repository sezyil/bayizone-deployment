<?php

namespace App\Http\Resources\Client\Customer;

use App\Models\Customer\CustomerOrder;
use App\Models\System\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerOrderListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        /** @var CustomerOrder[] $collection */
        $collection = $this->collection;


        foreach ($collection as $item) {
            /** @var CustomerOrder $item */
            /** @var Currency $curr */
            $curr = $item->getCurrency;
            $data[] = [
                'id' => $item->id,
                'company_customer_id' => $item->company_customer_id,
                'company_customer_name' => $item->companyCustomer?->company_name,
                'grand_total' => $item->grand_total,
                'formatted_grand_total' => $curr?->format($item->grand_total, true),
                'order_date' => $item->order_date,
                'order_no' => $item->order_no ?? '---',
                'currency' => $item->currency,
                'currency_name' => $item->getCurrency->title,
                'status' => $item->status,
                'preview_link' => $item->generatePreviewUri(),
            ];
        }

        return $data;
    }
}
