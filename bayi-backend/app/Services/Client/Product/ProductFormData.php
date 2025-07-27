<?php

namespace App\Services\Client\Product;

use App\Libraries\Client\FileManager\ProductFileManager;
use App\Libraries\Response\Responder;
use App\Libraries\Visenze\VisenzeCatalogApi;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductImage;
use App\Models\Customer\CustomerOfferLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Log;

class ProductFormData
{
    protected $customer_id;
    protected array $errors = [];
    protected array $formData = [];

    protected $product_id;

    protected array $names = [];
    protected  $sku;
    protected  $slug;
    protected string $model;
    protected  $upc;
    protected  $ean;
    protected  $mpn;
    protected int $quantity;

    protected int $unit_id;

    protected int $minimum;
    protected float $length;
    protected float $width;
    protected float $height;
    protected float $weight;
    protected int|float|null $volume;
    protected int|float|null $package;
    protected int $status;
    protected array $descriptions = [];
    protected float $price_tl;
    protected float $price_usd;
    protected float $price_euro;
    protected float $price_gbp;
    protected string $default_currency;
    protected bool $store_visibility;
    protected array $active_customer_group = [];

    protected array $images;
    protected array $attributes;
    protected array $variants = [];
    protected array $colors = [];
    protected array $dimensions = [];

    protected $links;

    public function __construct(string $customer_id, $formData, $product_id = null)
    {
        $this->customer_id = $customer_id;
        $this->formData = $formData;
        if ($product_id) {
            $this->product_id = $product_id;
        }

        $this->mapFormDataToProperties();
    }

    //parseFormData method
    private function mapFormDataToProperties()
    {
        $this->names = $this->formData['names'] ?? null;
        $this->sku = $this->formData['sku'] ?? null;
        $this->slug = $this->formData['slug'] ?? null;
        $this->model = $this->formData['model'] ?? null;
        $this->upc = $this->formData['upc'] ?? null;
        $this->ean = $this->formData['ean'] ?? null;
        $this->mpn = $this->formData['mpn'] ?? null;
        $this->quantity = $this->formData['quantity'] ?? null;
        $this->unit_id = (int)$this->formData['unit_id'] ?? null;
        $this->minimum = $this->formData['minimum'] ?? null;
        $this->length = $this->formData['length'] ?? null;
        $this->width = $this->formData['width'] ?? null;
        $this->height = $this->formData['height'] ?? null;
        $this->weight = $this->formData['weight'] ?? null;
        $this->status = $this->formData['status'] ?? null;
        $this->volume = $this->formData['volume'] ?? null;
        $this->package = $this->formData['package'] ?? null;
        $this->descriptions = $this->formData['descriptions'] ?? null;
        $this->price_tl = $this->formData['price_tl'] ?? null;
        $this->price_usd = $this->formData['price_usd'] ?? null;
        $this->price_euro = $this->formData['price_euro'] ?? null;
        $this->price_gbp = $this->formData['price_gbp'] ?? null;
        $this->default_currency = $this->formData['default_currency'] ?? null;
        $this->images = $this->formData['images'] ?? [];
        $this->attributes = $this->formData['attributes'] ?? [];
        $this->links = $this->formData['links'] ?? [];
        $this->variants = $this->formData['variants'] ?? [];
        $this->store_visibility = (bool)$this->formData['store_visibility'] ?? false;
        $this->active_customer_group = $this->formData['active_customer_group'] ?? [];
        $this->colors = $this->formData['colors'] ?? [];
        $this->dimensions = $this->formData['dimensions'] ?? [];
    }

    //process images method
    protected function processImages()
    {
        $product_id = $this->product_id;
        $savedImages = ProductImage::where('product_id', $this->product_id)->get();

        $formDataImages = [];
        foreach ($this->images as $image) {
            $formDataImages[] = [
                'image' => $image['image'],
                'sort_order' => $image['sort_order']
            ];
        }

        foreach ($savedImages as $savedImage) {
            if (!in_array($savedImage->image, array_column($formDataImages, 'image'))) {
                $savedImage->delete();
                // Depolamadan sil
                if (Storage::disk('public')->exists($savedImage->image)) {
                    Storage::disk('public')->delete($savedImage->image);
                }
            }
            $formDataImages = array_filter($formDataImages, function ($formDataImage) use ($savedImage) {
                return $formDataImage['image'] != $savedImage->image;
            });
        }
        foreach ($formDataImages as $image) {
            //if image not contains cache continue
            if (!str_contains($image['image'], 'cache')) {
                //rearrange sort order
                $productImage = ProductImage::where('product_id', $product_id)->where('image', $image)->first();
                $productImage->sort_order = $image['sort_order'];
                $productImage->save();
                continue;
            }
            $productImage = new ProductFileManager($this->customer_id, $product_id);
            $newImagePath = $productImage->storeCachedImage($image['image']);
            if ($newImagePath) {
                $productImage = new ProductImage();
                $productImage->product_id = $product_id;
                $productImage->image = $newImagePath;
                $productImage->sort_order = $image['sort_order'];
                $productImage->save();
            }
        }
    }

    //get result method
    protected function getResult(): JsonResponse
    {
        if (count($this->errors) > 0) {
            return Responder::send_unprocessable("Has Errors", $this->errors);
        }
        $product = Product::find($this->product_id);
        if ($product->exists()) {
            if ($product->customer?->ai_support) {
                try {
                    $visenzeApi = new VisenzeCatalogApi();
                    $req = $visenzeApi->createOrUpdate($product);
                    if ($req) {
                        $product->ai_sync = true;
                        $product->ai_last_sync = now();
                        $product->save();
                    } else {
                        //
                    }
                } catch (\Throwable $th) {
                    Log::error($th->getMessage());
                }
            }
        }
        return Responder::send_success("Success");
    }
}
