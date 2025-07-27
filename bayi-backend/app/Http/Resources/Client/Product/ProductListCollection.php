<?php

namespace App\Http\Resources\Client\Product;

use App\Enums\VariantTypesEnum;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\Catalog\Product\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $withOptions = $request->get('withOptions', false);

        $data = [];

        /** @var Product $item */
        foreach ($this->collection as $item) {
            $name = $item->description->name;
            $productData = [
                "id"        => $item->id,
                "sku"       => $item->sku,
                "name"      => $name,
                "model"     => $item->model,
                "image"     => $item->image,
                "quantity"  => $item->quantity,
                "default_currency" => $item->default_currency,
                "default_price" => $item->default_price,
                "price_tl"  => $item->price_tl,
                "price_usd" => $item->price_usd,
                "price_euro" => $item->price_euro,
                "price_gbp" => $item->price_gbp,
                "status"    => $item->status,
                "volume"    => $item->volume,
                "package"   => $item->package,
                'ai_sync'   => $item->ai_sync,
                "colors"        => $this->mapColors($item->variants()->colors()->get()),
                "dimensions"    => $this->mapDimensions($item->variants()->dimensions()->get()),
            ];

            if ($withOptions) {
                $productData['variants'] = $item->variants()->get()->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => VariantTypesEnum::getDescription($variant->type, app()->getLocale()),
                        'type' => $variant->type,
                        'value' => $variant->value,
                    ];
                }) ?? [];
            }


            $data[] = $productData;
        }

        return $data;
    }

    /**
     * @param ProductVariant $variants
     * @return mixed
     */
    private function mapColors($variants)
    {
        /*
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
                'value' => $variant->variantValues()->get()->map(function ($value) {
                    /** @var ProductVariantValue $value */
                    return [
                        'id' => $value->id,
                        'name' => $value->variantValue?->description()->name,
                        'root_variant_value_id' => $value->variantValue?->id ?? ''
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
    private function mapDimensions($variants)
    {
        $dimensions = [];

        /** @var ProductVariant[] $variants */
        foreach ($variants as $variant) {
            /** @var ProductVariant $variant */
            $dimensions[] = [
                'id' => $variant->id,
                'variant_id' => $variant->variant_id,
                'name' => $variant->variant?->description()->name,
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
