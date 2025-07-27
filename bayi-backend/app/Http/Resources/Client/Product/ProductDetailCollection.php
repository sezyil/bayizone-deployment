<?php

namespace App\Http\Resources\Client\Product;

use App\Enums\VariantTypesEnum;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductAttributes;
use App\Models\Catalog\Product\ProductCategories;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\Catalog\Product\ProductImage;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\Catalog\Product\ProductVariantValue;
use App\Models\System\Language;
use App\Models\VariantValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        /** @var Product $product */
        $product = $this->resource;

        /** @var ProductDescription $description */
        $description = $product->description()->get()->first();

        /** @var ProductImage[] $images */
        $images = $product->images()->get();

        /** @var ProductAttributes[] $attributes */
        $attributes = $product->attributes()->get();



        /** @var ProductCategories[] $categories */
        $categories = $product->categories()->get();

        $descriptions = $product->descriptions()->get();
        $languages = Language::all();
        $mappedDescriptions = $languages->mapWithKeys(function ($item) use ($descriptions) {
            $description = $descriptions->where('language', $item->code)->first();
            return [$item->code => $description ? $description->description : ''];
        });
        $mappedNames = $languages->mapWithKeys(function ($item) use ($descriptions) {
            $description = $descriptions->where('language', $item->code)->first();
            return [$item->code => $description ? $description->name : ''];
        });

        $response = [
            "name"          => $description->name,
            "names"         => $mappedNames,
            "sku"           => $product->sku,
            "model"         => $product->model,
            "upc"           => $product->upc,
            "ean"           => $product->ean,
            "mpn"           => $product->mpn,
            "quantity"      => $product->quantity,
            "unit_id"       => $product->unit_id,
            "minimum"       => $product->minimum,
            "length"        => $product->length,
            "width"         => $product->width,
            "height"        => $product->height,
            "weight"        => $product->weight,
            "status"        => (int)$product->status,
            "description"   => $description->description,
            "descriptions"  => $mappedDescriptions,
            "price_tl"      => $product->price_tl,
            "price_usd"     => $product->price_usd,
            "price_euro"    => $product->price_euro,
            "price_gbp"     => $product->price_gbp,
            "default_currency" => $product->default_currency,
            "volume"        => $product->volume,
            "package"       => $product->package,
            "images"        => [],
            "colors"        => $this->mapColors($product->variants()->colors()->get()),
            "dimensions"    => $this->mapDimensions($product->variants()->dimensions()->get()),
            "variants"      => $product->variants()->get()->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => VariantTypesEnum::getDescription($variant->type, app()->getLocale()),
                    'type' => $variant->type,
                    'value' => $variant->value,
                ];
            }) ?? [],
            "attributes"    => [],
            "links"         => [
                "categories" => []
            ],
            'store_visibility' => $product->store_visibility,
            'active_customer_group' => $product->active_customer_group,
        ];;

        foreach ($images as $k => $v) {
            $response['images'][] = [
                'id' => $v->id,
                'image' => $v->image,
                'sort_order' => $v->sort_order,
            ];
        }

        foreach ($attributes as $k => $v) {
            $response['attributes'][] = [
                'id' => $v->id,
                'attribute_id' => $v->attribute_id,
                'text' => $v->text,
            ];
        }





        foreach ($categories as $k => $v) {
            $response['links']['categories'][] = [
                'category_id' => $v->category_id,
            ];
        }

        return $response;
    }


    /**
     * @param ProductVariant $variants
     * @return mixed
     */
    public static function mapColors($variants)
    { /*
        'colors.*' => 'array',
            'colors.*.variant_id' => 'required|string',
            'colors.*.variant_value_id' => 'required|array',
            'colors.*.variant_value_id.*' => 'required|string',
        */
        $colors = [];
        /** @var ProductVariant[] $variants */
        foreach ($variants as $variant) {
            /** @var ProductVariant $variant */
            $colors[] = [
                'id' => $variant->id,
                'variant_id' => $variant->variant_id,
                'name' => $variant->variant?->getName(),
                'value' => $variant->variantValues()->get()->map(function ($value) {
                    /** @var ProductVariantValue $value */
                    return [
                        'id' => $value->id,
                        'variant_value_id' => $value->variant_value_id,
                        'name' => $value->variantValue->getName(),
                        'root_variant_value_id' => $value->variantValue?->id,
                    ];
                })
            ];
        }

        return $colors;
    }

    /**
     * @param ProductVariant $variants
     * @return mixed
     */
    public static function mapDimensions($variants)
    {
        $dimensions = [];

        /** @var ProductVariant[] $variants */
        foreach ($variants as $variant) {
            /** @var ProductVariant $variant */
            $dimensions[] = [
                'id' => $variant->id,
                'variant_id' => $variant->variant_id,
                'name' => $variant->variant?->getName(),
                'value' => $variant->variantValues()->get()->map(function ($value) {
                    /** @var ProductVariantValue $value */
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                    ];
                })
            ];
        }
        return $dimensions;
    }
}
