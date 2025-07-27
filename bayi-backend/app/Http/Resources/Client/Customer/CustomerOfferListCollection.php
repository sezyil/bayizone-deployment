<?php

namespace App\Http\Resources\Client\Customer;

use App\Models\Customer\CustomerOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerOfferListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        /** @var CustomerOffer[] $collection */
        $collection = $this->collection;


        foreach ($collection as $item) {
            /** @var CustomerOffer $item */
            $data[] = [
                'id' => $item->id,
                'company_customer_id' => $item->company_customer_id,
                'company_customer_name' => $item->company_customer?->company_name,
                'grand_total' => $item->grand_total,
                'formatted_grand_total' => $item->getCurrency?->format($item->grand_total,true),
                'offer_date' => $item->offer_date,
                'offer_due_date' => $item->offer_due_date,
                'offer_no' => $item->offer_no,
                'currency' => $item->currency,
                'currency_name' => $item->getCurrency->title,
                'mail_notification_date' => $item->mail_notification_date,
                'has_order' => $item->customer_order_id ? $item->customer_order_id : null,
                'status' => $item->status,
            ];
        }

        return $data;
    }
}
