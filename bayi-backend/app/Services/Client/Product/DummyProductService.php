<?php

namespace App\Services\Client\Product;

use App\Enums\VariantTypesEnum;
use App\Libraries\Client\FileManager\ProductFileManager;
use App\Models\Catalog\Product\Product;
use App\Models\System\Currency;
use Storage;
use Str;

class DummyProductService
{
    private $customer_id;
    private Product $product;
    public function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
        $this->product = $this->dummy();
    }

    private function dummy(): Product
    {
        // Dummy data for product
        $product = new Product();
        $product->customer_id = $this->customer_id;
        $product->model = Str::random(10);
        $product->quantity = 100;
        $product->image = null;
        $product->unit_id = 2; #adet
        $product->active_customer_group = [
            "CUSTOMER",
            "POTENTIAL",
            "SUPPLIER"
        ];
        $product->minimum = 1;
        $product->default_currency = Currency::DEFAULT_CURRENCY;
        $product->package = 1;
        $product->volume = 1;
        $product->weight = 1;
        $product->length = 1;
        $product->width = 1;
        $product->height = 1;
        $product->status = true;
        $product->price_tl = 100;

        $product->save();

        return $product;
    }

    //create descriptions
    private function createDescriptions()
    {
        $tr = [
            'language' => 'tr',
            'name' => 'Modern Köşe Koltuk Takımı',
            'description' => 'Modern Köşe Koltuk Takımı, evinizin şıklığını ve konforunu artırmak için tasarlanmıştır. Yüksek kaliteli kumaşı ve ergonomik yapısı ile hem şık hem de rahattır. Geniş oturma alanı ve estetik görünümü ile her türlü dekorasyon tarzına uyum sağlar. Ayrıca, dayanıklı malzemelerden üretilmiş olup uzun yıllar boyunca kullanım imkanı sunar.'
        ];
        $en = [
            'language' => 'en',
            'name' => 'Modern Corner Sofa Set',
            'description' => 'The Modern Corner Sofa Set is designed to enhance the elegance and comfort of your home. With its high-quality fabric and ergonomic structure, it is both stylish and comfortable. The spacious seating area and aesthetic appearance make it suitable for any decoration style. Additionally, it is made from durable materials, ensuring long-lasting use for years to come.',
        ];


        $this->product->descriptions()->createMany([$en, $tr]);
    }

    //create images
    private function createImages()
    {
        //in storage/dummp_product_images folder
        $manager = new ProductFileManager($this->customer_id, $this->product->id);
        $folder = $manager->getProductFolder();
        $_fileName = 'product.jpg';
        $newFilePath = $folder . $_fileName;

        $dummyPath = Storage::disk('dummy_product')->path($_fileName);
        file_put_contents($newFilePath, file_get_contents($dummyPath));

        $this->product->image = $newFilePath;
        $this->product->save();

        $this->product->images()->create([
            'image' => $newFilePath,
            'sort_order' => 1,
        ]);
    }

    //create variants
    private function createVariants()
    {
        $color = [
            [
                "en" => "yellow",
                "tr" => "sarı"
            ],
            [
                "en" => "red",
                "tr" => "kırmızı"
            ],
            [
                "en" => "blue",
                "tr" => "mavi"
            ]
        ];

        $dimensions = [
            [
                "width" => 40,
                "height" => 50,
                "length" => 30
            ],
            [
                "width" => 50,
                "height" => 60,
                "length" => 40
            ],
            [
                "width" => 60,
                "height" => 70,
                "length" => 50
            ]
        ];

        foreach ($color as $key => $value) {
            $this->product->variants()->create([
                'type' => VariantTypesEnum::COLOR->value,
                'value' => $value
            ]);
        }

        foreach ($dimensions as $key => $value) {
            $this->product->variants()->create([
                'type' => VariantTypesEnum::DIMENSION->value,
                'value' => $value
            ]);
        }
    }

    public function create(): Product
    {
        $this->createDescriptions();
        $this->createImages();
        $this->createVariants();
        /* $this->product->customer->subscription->decreaseProductCount(1); */
        return $this->product;
    }
}
