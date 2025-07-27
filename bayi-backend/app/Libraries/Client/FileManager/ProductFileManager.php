<?php

namespace App\Libraries\Client\FileManager;

use Illuminate\Support\Facades\Storage;

class ProductFileManager extends CustomerFileManager
{
    private $product_id;
    private $product_folder;

    /**
     * Constructor
     *
     * @param string $customer_id
     * @param string|integer $product_id
     */
    public function __construct(string $customer_id, $product_id)
    {
        parent::__construct($customer_id);
        $this->product_id = $product_id;

        $this->generateProductImagePath();
    }

    /**
     * Generate product image path
     *
     * @return void
     */
    public function generateProductImagePath()
    {
        //generate product image path
        $productImagePath = $this->customer_folder . 'product/' . $this->product_id . '/';
        if (!Storage::disk('public')->exists($productImagePath)) { //check if folder exists
            Storage::disk('public')->makeDirectory($productImagePath); //create folder
        }
        $this->product_folder = $productImagePath; //set product folder
    }


    /**
     * Store cached image to product folder
     *
     * @param string $cacheImageUri
     * @return string|null
     */
    public function storeCachedImage(string $cacheImageUri)
    {
        if (Storage::disk('public')->fileExists($cacheImageUri)) { //check if file exists in cache folder
            $fileName = pathinfo($cacheImageUri, PATHINFO_BASENAME); //get file name
            $newImagePath = $this->product_folder . $fileName; //generate new image path
            if (Storage::disk('public')->move($cacheImageUri, $newImagePath)) { //move file to product folder
                return $newImagePath; //return new image path
            }
        } else { //file not found
            return null;
        }
    }

    /**
     * Download From URL and store to product folder
     *
     * @param string $imageUri
     * @return string|null
     */
    public function downloadFromUrlAndStore(string $imageUri)
    {
        $fileName = pathinfo($imageUri, PATHINFO_BASENAME); //get file name
        $newImagePath = $this->product_folder . $fileName; //generate new image path
        if (Storage::disk('public')->put($newImagePath, file_get_contents($imageUri))) { //move file to product folder
            return $newImagePath; //return new image path
        } else { //file not found
            return null;
        }
    }


    /**
     * Delete all product images
     *
     * @return void
     */
    public function deleteProductImages()
    {
        Storage::disk('public')->deleteDirectory($this->product_folder);
    }

    //get product folder
    public function getProductFolder()
    {
        return $this->product_folder;
    }
}
