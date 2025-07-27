<?php

namespace App\Services\Store;

use App\Enums\VariantTypesEnum;
use App\Http\Resources\Client\Product\ProductDetailCollection;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\Customer;
use App\Models\StoreUser;
use App\Models\StoreUserCartItem;

class CartService
{
    private Customer $customer;
    private StoreUser $storeUser;
    public function __construct(Customer $customer, StoreUser $storeUser)
    {
        $this->customer = $customer;
        $this->storeUser = $storeUser;
    }

    /**
     * @return StoreUserCartItem[]|[]
     */
    private function cartItems()
    {
        return $this->storeUser->cartItems ?? [];
    }

    public function getCart()
    {
        $cart = $this->cartItems();
        $data = [];
        $productService = new ProductService($this->customer, $this->storeUser);
        foreach ($cart as $item) {
            $product = $productService->getProduct($item->product_uuid, false);
            if ($product) {
                $product['id'] = $item->id;
                $product['quantity'] = $item->quantity;
                $product['selected_color'] = $item->variants()->colors()->get()->map(function ($item) {
                    return $item->transformData(true, true);
                });
                $product['selected_dimension'] = $item->variants()->dimensions()->get()->map(function ($item) {
                    return $item->transformData(true, true);
                });
            }
            $data[] = $product;
        }
        return $data;
    }

    public function addCart(string $product_id, int $quantity, $color = null, $dimension = null): void
    {
        /** @var Product $product */
        $product = $this->customer->products()->where('uuid', $product_id)->first();
        if (!$product) {
            throw new \Exception("Product not found");
        }
        //if variant empty update quantity
        if (!$color && !$dimension) {
            $cartItem = $this->storeUser->cartItems()->where('product_uuid', $product->uuid)->doesntHave('variants')->first();
            if ($cartItem) {
                $cartItem->update([
                    'quantity' => (int)$cartItem->quantity + (int)$quantity,
                ]);
                return;
            }
        }

        $cartItem = $this->storeUser->cartItems()->create([
            'product_uuid' => $product->uuid,
            'quantity' => $quantity,
        ]);

        if ($cartItem) {
            if ($color) {
                foreach ($color as $item) {
                    /** @var ['id'=>'string', 'value'=>'string'] $color */
                    $findColor = $product->variants()->colors()->where('id', $item['id'])
                        ->first();
                    if ($findColor) {
                        $variantValue = $findColor->variantValues()->where('id', $item['value'])->first();
                        if ($variantValue) {
                            $cartItem->variants()->create([
                                'type' => VariantTypesEnum::COLOR->value,
                                'product_variant_id' => $findColor->id,
                                'product_variant_value_id' => $variantValue->id
                            ]);
                        }
                    }
                }
            }
            if ($dimension) {
                foreach ($dimension as $item) {
                    /** @var ['id'=>'string', 'value'=>'string'] $dimension */
                    $findDimension = $product->variants()->dimensions()->where('id', $item['id'])->first();
                    if ($findDimension) {
                        $variantValue = $findDimension->variantValues()->where('id', $item['value'])->first();
                        if ($variantValue) {
                            $cartItem->variants()->create([
                                'type' => VariantTypesEnum::DIMENSION->value,
                                'product_variant_id' => $findDimension->id,
                                'product_variant_value_id' => $variantValue->id
                            ]);
                        }
                    }
                }
            }
        }

        //add variants

    }

    public function removeCart(string $item_id): void
    {
        /** @var Product $product */
        $cartItem = $this->storeUser->cartItems()->where('id', $item_id)->first();
        if ($cartItem) {
            $cartItem->delete();
        }
    }

    public function clearCart(): void
    {
        $this->storeUser->cartItems()->delete();
    }

    //update cart item
    public function updateCart(string $product_id, int $quantity): void
    {
        $product = $this->customer->products()->where('uuid', $product_id)->first();
        if (!$product) {
            throw new \Exception("Product not found");
        }
        /** @var Product $product */
        $cartItem = $this->storeUser->cartItems()->where('product_uuid', $product->uuid)->first();
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $quantity,
            ]);
        }
    }
}
