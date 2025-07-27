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
use App\Models\Variant;
use DB;
use Str;

class UpdateProductService extends ProductFormData
{
    private Product $product_instance;
    private function saveProduct()
    {
        $product = &$this->product_instance;
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
        $product->length = $this->length;
        $product->width = $this->width ?? 0;
        $product->height = $this->height ?? 0;
        $product->weight = $this->weight ?? 0;
        $product->volume = $this->volume ?? 0;
        $product->package = $this->package ?? 0;
        $product->status = $this->status;
        $product->store_visibility = $this->store_visibility;
        $product->active_customer_group = $this->active_customer_group;
        $product->save();
    }

    private function saveProductDescription()
    {
        $productDescription = $this->product_instance->descriptions();
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
            ProductDescription::updateOrCreate(
                [
                    'product_id' => $this->product_id,
                    'language' => $lang
                ],
                $data
            );
        }
    }

    private function saveProductCategories()
    {
        $productCategory = $this->product_instance->categories();

        $productCategory->delete();

        if (isset($this->links['categories'])) {
            $categories = $this->links['categories'];
            foreach ($categories as $category) {
                $data = [
                    'product_id' => $this->product_id,
                    'category_id' => $category['category_id']
                ];
                $productCategory->create($data);
            }
        }
    }

    private function saveProductAttributes()
    {
        $productAttribute = $this->product_instance->attributes();
        //delete all attributes
        $productAttribute->delete();
        foreach ($this->attributes as $attribute) {
            $data = [
                'product_id' => $this->product_id,
                'attribute_id' => $attribute['attribute_id'],
                'language' => 'tr',
                'text' => $attribute['text']
            ];
            $productAttribute->create($data);
        }
    }

    private function saveVariants()
    {
        $currentColorIds = [];
        foreach ($this->colors as $color) {
            $data = [
                'type' => VariantTypesEnum::COLOR->value,
                'variant_id' => $color['variant_id'], // 'variant_id' => 'color_id
                'product_id' => $this->product_id,
            ];
            $productVariant = ProductVariant::updateOrCreate($data, $data);
            $currentColorIds[] = $productVariant->id;

            $currenColorValueIds = [];
            foreach ($color['variant_value_id'] as $variant_value_id) {
                $x = [
                    'product_variant_id' => $productVariant->id,
                    'variant_value_id' => $variant_value_id,
                    'type' => VariantTypesEnum::COLOR->value,
                ];
                $_z = $productVariant->variantValues()->updateOrCreate($x, $x);
                $currenColorValueIds[] = $_z->id;
            }
            $productVariant->variantValues()->whereNotIn('id', $currenColorValueIds)->delete();
        }
        $this->product_instance->variants()->colors()->whereNotIn('id', $currentColorIds)->delete();

        $currentDimensionIds = [];
        foreach ($this->dimensions as $dimension) {
            $data = [
                'type' => VariantTypesEnum::DIMENSION->value,
                'product_id' => $this->product_id,
                'variant_id' => $dimension['variant_id'], // 'variant_id' => 'dimension_id
            ];
            $productVariant = ProductVariant::updateOrCreate($data, $data);
            $currentDimensionIds[] = $productVariant->id;

            $currenDimValueIds = [];
            foreach ($dimension['value'] as $value) {
                $stdObj = new \stdClass();
                $stdObj->width = trim($value['width']) ?? 0;
                $stdObj->height = trim($value['height']) ?? 0;
                $stdObj->length = trim($value['length']) ?? 0;
                $x = [
                    'product_variant_id' => $productVariant->id,
                    'type' => VariantTypesEnum::DIMENSION->value,
                    'value' => $stdObj
                ];
                $xz = $productVariant->variantValues()->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(value, "$.width")) = ?', $stdObj->width)
                    ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(value, "$.height")) = ?', $stdObj->height)
                    ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(value, "$.length")) = ?', $stdObj->length)
                    ->first();
                if ($xz) {
                    $xz->update($x);
                    $currenDimValueIds[] = $xz->id;
                } else {
                    $_x = $productVariant->variantValues()->create($x);
                    $currenDimValueIds[] = $_x->id;
                }
            }
            $productVariant->variantValues()->whereNotIn('id', $currenDimValueIds)->delete();
        }
        $this->product_instance->variants()->dimensions()->whereNotIn('id', $currentDimensionIds)->delete();
    }



    private function process()
    {
        $this->product_id = $this->product_instance->id;
        $this->saveProduct();
        $this->saveProductDescription();
        $this->saveProductCategories();
        $this->saveVariants();
        $this->processImages();
        $this->saveProductAttributes();
    }

    public static function save(Product $productInstance, $formData)
    {
        $customer_id = $productInstance->customer_id;
        $service = new self($customer_id, $formData);
        $service->product_instance = $productInstance;
        $service->process();
        $product = Product::find($service->product_id);
        $product->triggerImageChange();
        return $service->getResult();
    }
}
