<?php

namespace App\Services\Store;

use App\Enums\VariantTypesEnum;
use App\Http\Resources\Client\Product\ProductDetailCollection;
use App\Models\Catalog\Category\Categories;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\Customer;
use App\Models\StoreUser;

class ProductService
{
    private Customer $customer;
    private StoreUser $storeUser;

    public function __construct(Customer $customer, StoreUser $storeUser)
    {
        $this->customer = $customer;
        $this->storeUser = $storeUser;
    }


    //find products / product
    public function getProducts($limit, $page, $search, $category_id): array
    {
        $result = $this->customer
            ->products()
            ->active()
            ->storeVisibility()
            ->when($category_id, function ($query, $category_id) {
                return $query->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id);
                });
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('description', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })->orWhereHas('descriptions', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
            })

            ->get();
        /*
            TODO: Implement pagination after
            ->paginate($limit, ['*'], 'page', $page); */

        /* $products = $result->items(); */
        $products = $result;
        $data = [];
        foreach ($products as $product) {
            $data[] = $this->transformProduct($product);
        }
        return $data;
    }

    public function getProduct(string $product_id, bool $counter = true): ?array
    {
        $product = $this->customer->products()
            ->active()
            ->storeVisibility()
            ->where('uuid', $product_id)->first();
        if (!$product) {
            throw new \Exception("Product not found");
        }
        /** @var Product $product */
        if ($this->storeUser && $counter) {
            if (!$this->storeUser->viewedProducts()->where('product_uuid', $product->uuid)->first()) {
                $this->storeUser->viewedProducts()->create([
                    'product_uuid' => $product->uuid,
                ]);
                $product->increment('view_count');
            }
        }

        return $this->transformProduct($product);
    }

    private function transformProduct(Product $product): array
    {
        return [
            'uuid' => $product->uuid,
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'images' => $product->images->pluck('image')->transform(fn ($image) => [
                'itemImageSrc' => url($image),
            ]),
            'quantity' => 1,
            'view_count' => $product->view_count,
            "colors" => ProductDetailCollection::mapColors($product->variants()->colors()->get()),
            "dimensions" => ProductDetailCollection::mapDimensions($product->variants()->dimensions()->get()),
            'detail' => [
                'length' => $product->length . ' cm',
                'width' => $product->width . ' cm',
                'height' => $product->height . ' cm',
                'weight' => $product->weight . ' kg',
                'volume' => $product->volume . ' m³',
                'package' => $product->package . ' pcs',
            ]
        ];
    }

    //get categories
    public function getCategories($search): array
    {
        $categories = Categories::where(function ($query) {
            $query->where('customer_id', $this->customer->id)
                ->orWhere('is_default', 1);
        })->when($search, function ($query, $search) {
            return $query->whereHas('description', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        })->whereHas('products', function ($query) {
            $query->whereHas('product', function ($query) {
                return $query->where('store_visibility', true)->where('customer_id', $this->customer->id);
            });
        })->get();

        $categories = Categories::MultilevelForStore($categories, '', 0, $this->customer->id);
        return $categories;
    }
}
