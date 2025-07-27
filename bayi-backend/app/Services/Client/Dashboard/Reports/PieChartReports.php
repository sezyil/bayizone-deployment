<?php

namespace App\Services\Client\Dashboard\Reports;

use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductCategories;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use App\Models\System\Countries;
use DB;

class PieChartReports
{
    public static function mostSellingProduct($customer_id, $limit = 5)
    {
        $bestProductQuery = "
        SELECT product_id, SUM(quantity) AS total
        FROM (
            SELECT product_id, quantity FROM customer_offer_lines WHERE customer_offer_id IN (
                SELECT id FROM customer_offers WHERE customer_id = ?
            )
            UNION ALL
            SELECT product_id, quantity FROM customer_order_lines WHERE customer_order_id IN (
                SELECT id FROM customer_orders WHERE customer_id = ?
            )
        ) AS best_seller
        GROUP BY product_id
        ORDER BY total DESC
        LIMIT 5
    ";

        // Sorguyu çalıştırma
        $topProductsByCustomer = DB::select($bestProductQuery, [$customer_id, $customer_id]) ?? [];
        if (!empty($topProductsByCustomer)) {
            $data = [];
            foreach ($topProductsByCustomer as $item) {
                 $product = Product::withTrashed()->where('id', $item->product_id)->first();
                $productName = $product?->getName() ?? CustomerOrderLine::whereProductId($item->product_id)->first()->product_name;
                /** @var Product $product */
                $data[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $productName,
                    'total' => $item->total
                ];
            }
            $topProductsByCustomer = $data;
        }

        return $topProductsByCustomer;
    }

    //most selling country
    public static function mostSellingCountry($customer_id, $limit = 5)
    {
        //get
        $orders = CustomerOrder::where('customer_id', $customer_id)->groupBy('billing_country_id')->selectRaw('billing_country_id, COUNT(*) as total')->limit(5)->get();
        $offers = CustomerOffer::where('customer_id', $customer_id)->groupBy('billing_country_id')->selectRaw('billing_country_id, COUNT(*) as total')->limit(5)->get();

        $countryData = [];

        foreach ($orders as $order) {
            /** @var CustomerOrder $order */
            $countryData[$order->billing_country_id] = [
                'country_id' => $order->billing_country_id,
                'country_name' => Countries::find($order->billing_country_id)->translateName(),
                'total' => $order->total
            ];
        }

        foreach ($offers as $offer) {
            /** @var CustomerOffer $offer */
            if (isset($countryData[$offer->billing_country_id])) {
                $countryData[$offer->billing_country_id]['total'] += $offer->total;
            } else {
                $countryData[$offer->billing_country_id] = [
                    'country_id' => $offer->billing_country_id,
                    'country_name' => Countries::find($offer->billing_country_id)->translateName(),
                    'total' => $offer->total
                ];
            }
        }

        //sort by total
        usort($countryData, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return array_slice($countryData, 0, $limit);
    }


    //most selling category
    public static function mostSellingCategory($customer_id, $limit = 5)
    {
        $bestProducts = self::mostSellingProduct($customer_id, $limit);
        $categoryData = [];
        foreach ($bestProducts as $product) {
            $categories = Product::withTrashed()->find($product['product_id'])->first();
            if (!$categories) {
                continue;
            }
            $categories = $categories->categories;
            foreach ($categories as $category) {
                /** @var ProductCategories $category */
                $category_id = $category->category_id;
                if (isset($categoryData[$category_id])) {
                    $categoryData[$category_id]['total'] += $product['total'];
                } else {
                    $categoryData[$category_id] = [
                        'category_id' => $category_id,
                        'category_name' => $category->category->getName(),
                        'total' => $product['total']
                    ];
                }
            }
        }

        //sort by total
        usort($categoryData, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return array_slice($categoryData, 0, $limit);
    }
}
