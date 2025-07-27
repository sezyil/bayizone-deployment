<?php

namespace App\Http\Resources\Client\Customer;

use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerOrderDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CustomerOrder $collection */
        $collection = $this->collection->first();
        $data = [
            'detail' => [
                'company_customer_id' => $collection->company_customer_id,
                'total_price' => $collection->total_price,
                'total_tax' => $collection->total_tax,
                'total_discount' => $collection->total_discount,
                'grand_total' => $collection->grand_total,
                'order_date' => $collection->order_date,
                'order_no' => $collection->order_no ?? '---',
                'currency' => $collection->currency,
                'note' => $collection->note,
                'billing_address' => $collection->billing_address,
                'billing_city_id' => $collection->billing_city_id,
                'billing_country_id' => $collection->billing_country_id,
                'billing_state_id' => $collection->billing_state_id,
                'status' => $collection->status,
                'is_international' => (bool)$collection->is_international,
                'delivery_type' => $collection->delivery_type,
                'payment_type' => $collection->payment_type,
                'total_volume' => $collection->total_volume,
                'total_package' => $collection->total_package,
                'incoterms' => $collection->incoterms,
            ],
            'lines' => $collection->lines()->get()->transform(function ($e) {
                /** @var CustomerOrderLine $e */
                return [
                    'product_id' => $e->product_id,
                    'product_name' => $e->product_name,
                    'product_code' => $e->product_code,
                    'product_unit' => $e->product?->unit?->description?->name ?? $e->product_unit,
                    'product_image_url' => $e->product_image_url,
                    'unit_id' => $e->unit_id,
                    'quantity' => $e->quantity,
                    'unit_price' => $e->unit_price,
                    'tax_rate' => $e->tax_rate,
                    'unit_discount_price' => $e->unit_discount_price,
                    'unit_discount_rate' => $e->unit_discount_rate,
                    'total_discount_price' => $e->total_discount_price,
                    'total_price' => $e->total_price,
                    'grand_total' => $e->grand_total,
                    'note' => $e->note,
                    'unit_volume' => $e->unit_volume,
                    'unit_package' => $e->unit_package,
                    'total_volume' => $e->total_volume,
                    'total_package' => $e->total_package,
                    'color' => $e->variants()->colors()->get()->transform(function ($v) {
                        return $v->transformData(true);
                    }) ?? null,
                    'dimension' => $e->variants()->dimensions()->get()->transform(function ($v) {
                        return $v->transformData(true);
                    }) ?? null,
                ];
            }),
        ];
        return $data;
    }
}
