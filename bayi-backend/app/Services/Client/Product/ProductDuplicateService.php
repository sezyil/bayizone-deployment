<?php

namespace App\Services\Client\Product;

use App\Libraries\Client\FileManager\ProductFileManager;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\Catalog\Product\ProductImage;
use Storage;
use Str;

class ProductDuplicateService
{
    private string $customer_id;
    private int $product_id;
    private Product $original_product;
    private ?Product $cloned_product;

    public function __construct(string $customer_id, int $product_id)
    {
        $this->customer_id = $customer_id;
        $this->product_id = $product_id;
        $this->original_product = Product::find($product_id);
    }

    public function duplicate(): int
    {
        $this->startDuplicate();
        return $this->cloned_product->id;
    }

    private function startDuplicate()
    {
        $this->createProduct();
        $this->createProductDescriptions();
        $this->createProductAttributes();
        $this->createProductCategories();
        $this->createVariants();
        $this->createProductImages();
    }


    private function createProduct()
    {
        $this->cloned_product = $this->original_product->replicate();
        $this->cloned_product->customer_id = $this->customer_id;
        $uuid = null;
        while (true) {
            $uuid = Str::uuid();
            $product = Product::where('uuid', $uuid)->first();
            if (!$product) {
                break;
            }
        }
        $this->cloned_product->uuid = $uuid;
        $model = null;
        $prefix = function () {
            return Str::random(12);
        };
        while (true) {
            $model = $prefix();
            $product = Product::where('model', $model)->where('customer_id', $this->customer_id)->first();
            if (!$product) {
                break;
            }
            $model = $prefix();
        }
        $this->cloned_product->model = $model;
        $this->cloned_product->view_count = 0;
        $this->cloned_product->ai_last_sync = null;
        $this->cloned_product->ai_sync = false;

        $this->cloned_product->save();
    }

    private function createProductImages()
    {
        $images = $this->original_product->images()->get();
        $fileManager = new ProductFileManager($this->customer_id, $this->cloned_product->id);
        $folderPath = $fileManager->getProductFolder();
        $i = 0;
        $mainImg = null;
        foreach ($images as $image) {
            /** @var ProductImage $image */
            $imageName = pathinfo($image->image, PATHINFO_BASENAME);
            $newPath = $folderPath . Str::uuid() . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
            $copiedImg = $this->copyImageFile($image->image, $newPath);
            if (!$copiedImg) {
                continue;
            }

            $cloneImage = $image->replicate();
            $cloneImage->image = $newPath;
            $cloneImage->product_id = $this->cloned_product->id;
            $cloneImage->sort_order = $i;
            $cloneImage->save();
            if ($i == 0) {
                $mainImg = $newPath;
            }

            $i++;
        }

        if ($mainImg) {
            $this->cloned_product->image = $mainImg;
            $this->cloned_product->save();
        }
    }

    private function copyImageFile($path, $newPath)
    {
        $copied = false;
        if (Storage::disk('public')->exists($path)) {
            $copied = Storage::disk('public')->copy($path, $newPath);
        }
        return $copied;
    }

    //description
    private function createProductDescriptions()
    {
        $descriptions = $this->original_product->descriptions()->get();
        foreach ($descriptions as $description) {
            /** @var ProductDescription $description */
            $cloneDescription = $description->replicate();
            $cloneDescription->name = $description->name . ' - Copy';
            $cloneDescription->product_id = $this->cloned_product->id;
            $cloneDescription->save();
        }
    }

    //attributes
    private function createProductAttributes()
    {
        $attributes = $this->original_product->attributes()->get();
        foreach ($attributes as $attribute) {
            $cloneAttribute = $attribute->replicate();
            $cloneAttribute->product_id = $this->cloned_product->id;
            $cloneAttribute->save();
        }
    }

    //categories
    private function createProductCategories()
    {
        $categories = $this->original_product->categories()->get();
        foreach ($categories as $category) {
            $cloneCategory = $category->replicate();
            $cloneCategory->product_id = $this->cloned_product->id;
            $cloneCategory->save();
        }
    }

    //variants
    private function createVariants()
    {
        $variants = $this->original_product->variants()->get();
        foreach ($variants as $variant) {
            $cloneVariant = $variant->replicate();
            $cloneVariant->product_id = $this->cloned_product->id;
            $cloneVariant->save();
        }
    }
}
