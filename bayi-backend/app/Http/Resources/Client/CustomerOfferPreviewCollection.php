<?php

namespace App\Http\Resources\Client;

use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOfferLine;
use App\Models\Customer\CustomerOfferLineVariant;
use App\Models\System\Cities;
use App\Models\System\Countries;
use App\Models\System\States;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerOfferPreviewCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CustomerOffer $collection */
        $collection = $this->resource;
        $customerData = $collection->customer()->get()->transform(function ($e) {
            /** @var Customer $e */
            return [
                'firm_name' => $e->firm_name,
                'tax_no' => $e->tax_no,
                'tax_administration' => $e->tax_administration,
                'address' => $e->address,
                'country' => Countries::find($e->country_id)->name ?? null,
                'state' => States::find($e->state_id)->name ?? null,
                'city' => Cities::find($e->city_id)->name ?? null,
                'postcode' => $e->postcode,
                'email' => $e->email,
                'phone' => $e->phone,
            ];
        })->first();
        $companyCustomerData = $collection->company_customer()->get()->transform(function ($e) {
            /** @var CompanyCustomer $e */
            return [
                'authorized_name' => $e->authorized_name,
                'tax_office' => $e->tax_office,
                'tax_identity_no' => $e->tax_identity_no,
                'company_name' => $e->company_name,
                'phone' => $e->phone,
                'fax' => $e->fax,
                'email' => $e->email,
                'address' => $e->address,
                'country_id' => Countries::find($e->country_id)->name ?? null,
                'state_id' => States::find($e->state_id)->name ?? null,
                'city_id' => Cities::find($e->city_id)->name ?? null,
                'postcode' => $e->postcode,
                'type' => $e->type,
            ];
        })->first();
        $data = [
            'company_customer_id' => $collection->company_customer_id,
            'total_price' => $collection->total_price,
            'formatted_total_price' => $collection->getCurrency->format($collection->total_price, true), // '100,00 ₺'
            'total_tax' => $collection->total_tax,
            'formatted_total_tax' => $collection->getCurrency->format($collection->total_tax, true),
            'total_discount' => $collection->total_discount,
            'formatted_total_discount' => $collection->getCurrency->format($collection->total_discount, true),
            'grand_total' => $collection->grand_total,
            'formatted_grand_total' => $collection->getCurrency->format($collection->grand_total, true),
            'offer_date' => $collection->offer_date,
            'offer_due_date' => $collection->offer_due_date,
            'offer_no' => $collection->offer_no,
            'currency' => $collection->currency,
            'note' => $collection->note,
            'billing_address' => $collection->billing_address,
            'billing_city' => Cities::find($collection->billing_city_id)->name ?? null,
            'billing_state' => States::find($collection->billing_state_id)->name ?? null,
            'billing_country' => Countries::find($collection->billing_country_id)->name ?? null,
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
            'international_order' => $collection->international_order,
            'status' => $collection->status,
            //format d-m-Y H:i:s
            'created_at' => $collection->created_at->toDateTime()->format('d-m-Y H:i:s'), // '2021-01-01 00:00:00
            'updated_at' => $collection->updated_at->toDateTime()->format('d-m-Y H:i:s'), // '2021-01-01 00:00:00
            'lines' => [],
            'offer_uri' => $collection->generatePreviewUri(),
            'company_customer' => $companyCustomerData,
            'customer' => $customerData,
        ];

        foreach ($collection->lines()->get() as $line) {
            /** @var CustomerOfferLine $line */
            $tmpLine = [
                'id' => $line->id,
                'product_id' => $line->product_id,
                'product_name'  => $line->product?->getName() ?? $line->product_name,
                'product_code' => $line->product_code,
                'product_unit' => $line->product?->unit?->description?->name ?? $line->product_unit,
                'product_image_url' => $line->product_image_url,
                'unit_name' => $line->unitType->name ?? null,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'formatted_unit_price' => $collection->getCurrency->format($line->unit_price, true),
                'tax_rate' => $line->tax_rate,
                'unit_discount_price' => $line->unit_discount_price,
                'unit_discount_rate' => $line->unit_discount_rate,
                'total_discount_price' => $line->total_discount_price,
                'total_price' => $line->total_price,
                'formatted_total_price' => $collection->getCurrency->format($line->total_price, true),
                'grand_total' => $line->grand_total,
                'formatted_grand_total' => $collection->getCurrency->format($line->grand_total, true),
                'note' => $line->note,
                'color' => $line->variants()->colors()->get()->map(function ($e) {
                    /** @var CustomerOfferLineVariant $e */
                    return $e->transformData(true);
                }) ?? null,
                'dimension' => $line->variants()->dimensions()->get()->map(function ($e) {
                    /** @var CustomerOfferLineVariant $e */
                    return $e->transformData(true);
                }) ?? null,
            ];
            $data['lines'][] = $tmpLine;
        }

        return $data;
    }
}
