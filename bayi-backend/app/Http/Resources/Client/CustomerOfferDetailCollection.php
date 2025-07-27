<?php

namespace App\Http\Resources\Client;

use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOfferLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerOfferDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CustomerOffer $collection */
        $collection = $this->collection->first();
        $data = [
            'detail' => [
                'company_customer_id' => $collection->company_customer_id,
                'total_price' => $collection->total_price,
                'total_tax' => $collection->total_tax,
                'total_discount' => $collection->total_discount,
                'grand_total' => $collection->grand_total,
                'offer_date' => $collection->offer_date,
                'offer_due_date' => $collection->offer_due_date,
                'offer_no' => $collection->offer_no,
                'currency' => $collection->currency,
                'note' => $collection->note,
                'billing_address' => $collection->billing_address,
                'billing_city_id' => $collection->billing_city_id,
                'billing_country_id' => $collection->billing_country_id,
                'billing_state_id' => $collection->billing_state_id,
                'billing_zip_code' => $collection->billing_zip_code,
                'payment_bank_name' => $collection->payment_bank_name,
                'payment_branch_name' => $collection->payment_branch_name,
                'payment_account_name' => $collection->payment_account_name,
                'payment_account_number' => $collection->payment_account_number,
                'payment_iban' => $collection->payment_iban,
                'payment_swift_code' => $collection->payment_swift_code,
                'contact_person' => $collection->contact_person,
                'contact_email' => $collection->contact_email,
                'contact_phone' => $collection->contact_phone,
                'whatsapp_notification_date' => $collection->whatsapp_notification_date,
                'mail_notification_date' => $collection->mail_notification_date,
                'password' => $collection->password,
                'is_request' => $collection->is_request,
                'is_international' => (bool)$collection->international_order,
                'delivery_type' => $collection->delivery_type,
                'payment_type' => $collection->payment_type,
                'visible_columns' => $collection->visible_columns,
                'status' => $collection->status,
                'incoterms' => $collection->incoterms,
            ],
            'lines' => $collection->lines()->get()->transform(function ($e) {
                /** @var CustomerOfferLine $e */
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
