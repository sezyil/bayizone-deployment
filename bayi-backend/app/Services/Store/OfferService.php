<?php

namespace App\Services\Store;

use App\Enums\CompanyCustomerGroupEnum;
use App\Enums\CustomerTypesEnum;
use App\Enums\VariantTypesEnum;
use App\Http\Requests\Store\OfferRequest;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariantValue;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
use App\Models\OfferRequest as ModelsOfferRequest;
use App\Models\StoreUser;

class OfferService
{
    private Customer $customer;
    private StoreUser $storeUser;
    private CompanyCustomer $companyCustomer;
    private $formData;

    public function __construct(Customer $customer, StoreUser $storeUser)
    {
        $this->customer = $customer;
        $this->storeUser = $storeUser;
    }

    //set data
    public function setFormData(array $data): self
    {
        $this->formData = $data;
        return $this;
    }

    private function getFormData(string $type): array
    {
        $availableTypes = ['cart', 'customer'];
        if (!in_array($type, $availableTypes)) {
            throw new \Exception('Type not found');
        }
        return $this->formData[$type];
    }

    public function createOffer()
    {
        $this->createCustomer();
        $customerData = $this->getFormData('customer');
        $cartData = $this->getFormData('cart');
        $offer = ModelsOfferRequest::create([
            'customer_id' => $this->customer->id,
            'company_customer_id' => $this->companyCustomer->id,
            'global_note' => $customerData['note'] ?? null,
            'from_store' => true,
            'currency' => $customerData['currency'],
        ]);

        foreach ($cartData as $product) {
            $note = $product['note'] ?? null;

            $line = $offer->lines()->create([
                'product_id' => Product::where('uuid', $product['uuid'])->first()->id,
                'quantity' => $product['quantity'],
                'note' => $note
            ]);

            $color = isset($product['color']) && is_array($product['color']) ? $product['color'] : [];
            if ($color) {
                foreach ($color as $colorItem) {
                    $productVariantValue = ProductVariantValue::where('product_variant_id', $colorItem['product_variant_id'])
                        ->where('id', $colorItem['product_variant_value_id'])
                        ->first();
                    if ($productVariantValue) {
                        $line->variants()->create([
                            'type' => VariantTypesEnum::COLOR->value,
                            'product_variant_id' => $colorItem['product_variant_id'],
                            'product_variant_value_id' => $productVariantValue->id,
                        ]);
                    }
                }
            }

            $dimension = isset($product['dimension']) && is_array($product['dimension']) ? $product['dimension'] : [];
            if ($dimension) {
                foreach ($dimension as $dimensionItem) {
                    $productVariantValue = ProductVariantValue::where('product_variant_id', $dimensionItem['product_variant_id'])
                        ->where('id', $dimensionItem['product_variant_value_id'])
                        ->first();
                    if ($productVariantValue) {
                        $line->variants()->create([
                            'type' => VariantTypesEnum::DIMENSION->value,
                            'product_variant_id' => $dimensionItem['product_variant_id'],
                            'product_variant_value_id' => $productVariantValue->id,
                        ]);
                    }
                }
            }
        }

        return $offer;
    }


    private function createCustomer(): void
    {
        $customerFormData = $this->getFormData('customer');
        $companyCustomer = $this->customer->companyCustomers->where('email', $customerFormData['email'])->first();
        if (!$companyCustomer) {
            $customerType = $customerFormData['type'] == 'corporate' ? CustomerTypesEnum::LEGAL : CustomerTypesEnum::REAL;
            $this->companyCustomer = $this->customer->companyCustomers()->create([
                'customer_id' => $this->customer->id,
                'authorized_name' => $customerFormData['authorized_name'],
                'group' => CompanyCustomerGroupEnum::POTENTIAL,
                'company_name' => $customerFormData['company_name'] ?? $customerFormData['authorized_name'],
                'phone' => $customerFormData['phone'],
                'email' => $customerFormData['email'],
                'address' => $customerFormData['address'] ?? '---',
                'country_id' => (int)$customerFormData['country_id'],
                'state_id' => (int)$customerFormData['state_id'] == 0 ? null : (int)$customerFormData['state_id'],
                'city_id' => (int)$customerFormData['city_id'] == 0 ? null : (int)$customerFormData['city_id'],
                'type' => $customerType,
                'status' => 1
            ]);
        } else {
            $this->companyCustomer = $companyCustomer;
        }
    }
}
