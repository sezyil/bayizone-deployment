<?php

namespace App\Http\Resources\Dealer;

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
        $data = [];

        /** @var Product $item */
        foreach ($this->collection as $item) {
            $name = $item->description?->name ?? $item->descriptions->first()->name;
            $images = $item->images()->get();
            $data[] = [
                "id"        => $item->uuid,
                "sku"       => $item->sku,
                "name"      => $name,
                "model"     => $item->model,
                "image"     => $item->image,
                "quantity"  => $item->quantity,
                "width"     => $item->width,
                "height"    => $item->height,
                "length"    => $item->length,
                "weight"    => $item->weight,
                "package"   => $item->package,
                "volume"    => $item->volume,
                "images" => $images->transform(function ($item) {
                    return  $item->image;
                }),
                "colors" => $this->mapColors($item->variants()->colors()->get()),
                "dimensions" => $this->mapDimensions($item->variants()->dimensions()->get()),
            ];
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
                'name' => $variant->getName(),
                'value' => $variant->variantValues()->get()->map(function ($value) {
                    /** @var ProductVariantValue $value */
                    return [
                        'id' => $value->id,
                        'name' => $value->variantValue->description()->name,
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
                'name' => $variant->getName(),
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
