<?php

namespace App\Services\Client\Product;

use App\Libraries\Client\FileManager\ProductFileManager;
use App\Models\Catalog\Product\Product;

class DeleteProductService
{
    private string $customer_id;
    private int $product_id;
    public function __construct(string $customer_id, int $product_id)
    {
        $this->customer_id = $customer_id;
        $this->product_id = $product_id;
    }

    public static function delete(string $customer_id, int $product_id, bool $forceDelete = false): bool
    {
        $service = new DeleteProductService($customer_id, $product_id);
        $productModel = Product::where('customer_id', '=', $service->customer_id)
            ->where('id', '=', $service->product_id)
            ->first();

        if ($productModel) {
            if ($forceDelete) {
                $productModel->forceDelete();
                (new ProductFileManager($service->customer_id, $service->product_id))->deleteProductImages(); //delete product folder
            }else{
                $productModel->delete(); //delete product
            }
            return true;
        }
        return false;
    }
}
