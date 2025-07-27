<?php

namespace App\Libraries\Visenze;

use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductImage;
use GuzzleHttp\Client;
use Log;

class VisenzeCatalogApi
{
    //access_key 8a5eb2687ae081f1d6276267335ccbe88a82248b
    //scret key f25742a221353482fe1455d38c6ab2cb2307bed6
    const BASE_URL = "https://catalog.api.visenze.com/v1/dataset/1/";
    private Client $client;

    public function __construct()
    {
        $this->initClient();
    }

    private function initClient()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic OGE1ZWIyNjg3YWUwODFmMWQ2Mjc2MjY3MzM1Y2NiZTg4YTgyMjQ4YjpmMjU3NDJhMjIxMzUzNDgyZmUxNDU1ZDM4YzZhYjJjYjIzMDdiZWQ2',
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function createProduct(Product $product)
    {
        $requestData = [];
        $requestData[] = [
            'product_id' => $product->uuid,
            'main_image_url' => $product->getThumbWithUrl(),
            'additional_image_url' => $product->images()->get()->map(function ($image) {
                /** @var ProductImage $image */
                return $image->getWithUrl();
            }),
            'title' => $product->getName(),
            'product_url' => $product->getStoreUrl(),
            'description' => $product->getDescription(),
        ];

        return $this->client->post('data', [
            'json' => ['data_list' => $requestData],
        ]);
    }

    public function updateProduct(Product $product)
    {
        $requestData = [];
        $requestData[] =  [
            'product_id' => $product->uuid,
            'main_image_url' => $product->getThumbWithUrl(),
            'additional_image_url' => $product->images()->get()->map(function ($image) {
                /** @var ProductImage $image */
                return $image->getWithUrl();
            }),
            'title' => $product->getName(),
            'product_url' => $product->getStoreUrl(),
            'description' => $product->getDescription(),
        ];

        return $this->client->patch('data', [
            'json' => ['data_list' => $requestData],
        ]);
    }
    public function getProduct(Product $product)
    {
        return $this->client->get('data/' . $product->uuid);
    }

    public function createOrUpdate(Product $product)
    {
        $exist = false;
        try {
            $this->getProduct($product);
            $exist = true;
        } catch (\Throwable $th) {
        }

        try {
            $response = null;
            if ($exist) $response = $this->updateProduct($product);
            else $response = $this->createProduct($product);
            if ($response) {
                $contents = $response->getBody()->getContents();
                Log::debug($contents);
                $msg = json_decode($contents);
                if ($msg?->result?->trans_id) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            throw new \Exception($th);
        }
    }
}
