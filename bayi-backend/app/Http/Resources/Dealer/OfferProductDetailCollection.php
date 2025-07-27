<?php

namespace App\Http\Resources\Dealer;


use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductAttributes;
use App\Models\Catalog\Product\ProductCategories;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\Catalog\Product\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductDetailCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {

        /** @var Product $collection */
        $collection = $this->resource;

        /** @var ProductDescription $description */
        $description = $collection?->description ?? $collection->descriptions->first();

        /** @var ProductImage[] $images */
        $images = $collection->images()->get();

        /** @var ProductAttributes[] $attributes */
        $attributes = $collection->attributes()->get();



        /** @var ProductCategories[] $categories */
        $categories = $collection->categories()->get();



        $response = [
            "name"          => $description->name,
            "sku"           => $collection->sku,
            "model"         => $collection->model,
            "upc"           => $collection->upc,
            "ean"           => $collection->ean,
            "mpn"           => $collection->mpn,
            "quantity"      => $collection->quantity,
            "unit_id"       => $collection->unit_id,
            "minimum"       => $collection->minimum,
            "length"        => $collection->length,
            "width"         => $collection->width,
            "height"        => $collection->height,
            "weight"        => $collection->weight,
            "status"        => (int)$collection->status,
            "description"   => $description->description,
            "price"         => $collection->price,
            "images"        => [],
            "attributes"    => [],
            "links"         => [
                "categories" => []
            ],
            "variants"      => $collection->variants()->get()->transform(function ($v) {
                return [
                    'type' => $v->type,
                    'value' => $v->value,
                ];
            }),
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
}
