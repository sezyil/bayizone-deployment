<?php

namespace App\Http\Controllers\Dealer\OfferRequest;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dealer\OfferProductDetailCollection;
use App\Http\Resources\Dealer\ProductListCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\Responder;
use App\Models\Catalog\Product\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OfferRequestProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $limit = request()->get('limit', 10);
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $limit;
        $user = SanctumDealerHelper::getUser();
        $customer_id = $user->customer_id;
        $company_group = $user->companyCustomer->group;
        $products = Product::where('customer_id', $customer_id)
            ->active()
            ->activeCustomerGroup($company_group)
            ->paginate($limit, ['*'], 'page', $page);
        $data = $products->items();
        $count = $products->total();

        return Responder::send_success("", [
            'items' => new ProductListCollection($data),
            'total' => $count,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        /* $customer_id = SanctumDealerHelper::customer_id();
        $product = Product::where('customer_id', $customer_id)
            ->where('status', 1)
            ->where('uuid', $id)->firstOrFail();
        if (!$product->exists()) {
            return Responder::send_not_found();
        }

        $response = new OfferProductDetailCollection($product);
        return Responder::send_success("", $response); */
        return Responder::send_success("", []);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        //
    }
}
