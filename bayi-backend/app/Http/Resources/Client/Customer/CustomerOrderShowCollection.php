<?php

namespace App\Http\Resources\Client\Customer;

use App\Enums\CustomerOfferStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderHistory;
use App\Models\Customer\CustomerOrderLine;
use App\Models\Customer\CustomerOrderLineHistory;
use App\Models\System\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrderShowCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CustomerOrder $collection */
        $collection = $this->resource;
        /** @var Currency $currency */
        $currency = $collection->getCurrency;

        $currFormat = fn ($value) => $currency->format($value, true);

        $data = [
            'detail' => [
                'company_customer_id' => $collection->company_customer_id,
                'company_customer_name' => $collection->companyCustomer->company_name,
                'total_price' => $currFormat($collection->total_price),
                'total_tax' => $currFormat($collection->total_tax),
                'total_discount' => $currFormat($collection->total_discount),
                'grand_total' => $currFormat($collection->grand_total),
                'order_date' => Carbon::parse($collection->order_date)->format('d.m.Y'),
                'order_no' => $collection->order_no ?? '---',
                'currency' => $collection->currency,
                'note' => $collection->note,
                'billing_address' => $collection->billing_address,
                'billing_city_id' => $collection->billing_city_id,
                'billing_country_id' => $collection->billing_country_id,
                'billing_state_id' => $collection->billing_state_id,
                'status' => $collection->status,
                'status_label' => CustomerOrderStatusEnum::description($collection->status),
                'is_international' => (bool)$collection->is_international,
                'delivery_type' => $collection->delivery_type,
                'payment_type' => $collection->payment_type,
                'total_volume' => $collection->total_volume,
                'total_package' => $collection->total_package,
                'incoterms' => $collection->incoterms,
                'managed_by_system' => $collection->managed_by_system,
                'created_at' => $collection->created_at->format('d.m.Y H:i'),
                'updated_at' => $collection->updated_at->format('d.m.Y H:i'),
            ],
            'lines' => $collection->lines()->orderBy('created_at', 'asc')->get()->transform(function ($e) use ($currFormat) {
                /** @var CustomerOrderLine $e */
                return [
                    'id' => $e->id,
                    'product_id' => $e->product_id,
                    'product_name' => $e->product?->description?->name ?? $e->product_name,
                    'product_code' => $e->product_code,
                    'product_unit' => $e->product?->unit?->description?->name ?? $e->product_unit,
                    'product_image_url' => $e->product_image_url,
                    'unit_id' => $e->unit_id,
                    'quantity' => $e->quantity,
                    'unit_price' => $currFormat($e->unit_price),
                    'tax_rate' => $e->tax_rate,
                    'unit_discount_price' => $e->unit_discount_price,
                    'unit_discount_rate' => $e->unit_discount_rate,
                    'total_discount_price' => $e->total_discount_price,
                    'total_price' => $currFormat($e->total_price),
                    'grand_total' => $currFormat($e->grand_total),
                    'note' => $e->note,
                    'unit_volume' => $e->unit_volume,
                    'unit_package' => $e->unit_package,
                    'total_volume' => $e->total_volume,
                    'total_package' => $e->total_package,
                    'remaining_quantity' => $e->remaining_quantity,
                    'sent_quantity' => $e->sent_quantity,
                    'status' => $e->status,
                    'status_label' => $e->getStatusLabel(),
                    'color' => $e->variants()->colors()->get()->transform(function ($v) {
                        return $v->transformData(true,true);
                    }) ?? null,
                    'dimension' => $e->variants()->dimensions()->get()->transform(function ($v) {
                        return $v->transformData(true,true);
                    }) ?? null,
                    'history' => $e->histories()->orderByDesc('id')->get()->transform(function ($h) {
                        /** @var CustomerOrderLineHistory $h */
                        return [
                            'id' => $h->id,
                            'customer_order_line_id' => $h->customer_order_line_id,
                            'status' => $h->status,
                            'status_label' => $h->getStatusLabel(),
                            'note' => $h->note,
                            'notify' => $h->notify,
                            'created_at' => $h->created_at->format('d.m.Y H:i'),
                        ];
                    }),
                ];
            }),
            'histories' => $collection->histories()->orderByDesc('id')->get()->transform(function ($e) {
                /** @var CustomerOrderHistory $e */
                return [
                    'id' => $e->id,
                    'status' => $e->status_code,
                    'status_label' => $e->getStatusText(),
                    'note' => $e->note,
                    'created_at' => $e->created_at->format('d.m.Y H:i'),
                    'notify' => $e->notify,
                ];
            }),
        ];
        return $data;
    }
}
