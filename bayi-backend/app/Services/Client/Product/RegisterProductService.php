<?php

namespace App\Services\Client\Product;

use App\Enums\VariantTypesEnum;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\Responder;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductAttributes;
use App\Models\Catalog\Product\ProductCategories;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\Catalog\Product\ProductImage;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\Customer;
use App\Models\System\Language;
use Str;

class RegisterProductService extends ProductFormData
{
    private function saveProduct()
    {
        $product = new Product();
        $product->customer_id = $this->customer_id;
        $product->sku = $this->sku;
        $product->model = $this->model;
        $product->upc = $this->upc;
        $product->ean = $this->ean;
        $product->mpn = $this->mpn;
        $product->quantity = $this->quantity;
        $product->unit_id = $this->unit_id ?? null;
        $product->price_tl = $this->price_tl;
        $product->price_usd = $this->price_usd;
        $product->price_euro = $this->price_euro;
        $product->price_gbp = $this->price_gbp;
        $product->default_currency = $this->default_currency;
        $product->minimum = $this->minimum;
        $product->length = $this->length ?? 0;
        $product->width = $this->width ?? 0;
        $product->height = $this->height ?? 0;
        $product->weight = $this->weight ?? 0;
        $product->volume = $this->volume ?? 0;
        $product->package = $this->package ?? 0;
        $product->status = $this->status;
        $product->store_visibility = $this->store_visibility;
        $product->active_customer_group = $this->active_customer_group;
        $product->save();
        $this->product_id = $product->id;
    }

    private function saveProductDescription()
    {
        $langs = Language::all()->mapWithKeys(function ($lang) {
            return [$lang->code => []];
        });
        foreach ($langs as $lang => $value) {
            $data = [
                'product_id' => $this->product_id,
                'language' => $lang,
                'name' => $this->names[$lang],
                'description' => $this->descriptions[$lang] ?? ''
            ];
            ProductDescription::create($data);
        }
    }

    private function saveProductCategories()
    {
        if (isset($this->links['categories'])) {
            $categories = $this->links['categories'];
            foreach ($categories as $category) {
                $data = [
                    'product_id' => $this->product_id,
                    'category_id' => $category['category_id']
                ];
                $productCategory = new ProductCategories();
                $productCategory->fill($data);
                $productCategory->save();
            }
        }
    }

    private function saveProductAttributes()
    {
        foreach ($this->attributes as $attribute) {
            $data = [
                'product_id' => $this->product_id,
                'attribute_id' => $attribute['attribute_id'],
                'language' => 'tr',
                'text' => $attribute['text']
            ];
            $productAttribute = new ProductAttributes();
            $productAttribute->fill($data);
            $productAttribute->save();
        }
    }

    private function saveVariants()
    {
        foreach ($this->colors as $color) {
            $data = [
                'type' => VariantTypesEnum::COLOR->value,
                'variant_id' => $color['variant_id'], // 'variant_id' => 'color_id
                'product_id' => $this->product_id,
            ];
            $productVariant = new ProductVariant();
            $productVariant->fill($data);
            $productVariant->save();

            foreach ($color['variant_value_id'] as $variant_value_id) {
                $x = [
                    'product_variant_id' => $productVariant->id,
                    'variant_value_id' => $variant_value_id,
                    'type' => VariantTypesEnum::COLOR->value,
                ];
                $productVariant->variantValues()->create($x);
            }
        }

        foreach ($this->dimensions as $dimension) {
            $data = [
                'type' => VariantTypesEnum::DIMENSION->value,
                'product_id' => $this->product_id,
                'variant_id' => $dimension['variant_id'], // 'variant_id' => 'dimension_id
            ];
            $productVariant = new ProductVariant();
            $productVariant->fill($data);
            $productVariant->save();

            foreach ($dimension['value'] as $value) {
                $x = [
                    'product_variant_id' => $productVariant->id,
                    'type' => VariantTypesEnum::DIMENSION->value,
                    'value' => $value,
                ];
                $productVariant->variantValues()->create($x);
            }
        }
    }



    private function process()
    {
        $this->saveProduct();
        $this->saveProductDescription();
        $this->saveProductCategories();
        $this->saveVariants();
        $this->processImages();
        $this->saveProductAttributes();
    }

    public static function save(string $customer_id, $formData)
    {
        $service = new RegisterProductService($customer_id, $formData);
        $service->process();
        $product = Product::find($service->product_id);
        $product->triggerImageChange();

        return $service->getResult();
    }
}
