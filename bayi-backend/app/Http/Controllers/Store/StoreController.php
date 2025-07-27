<?php

namespace App\Http\Controllers\Store;

use App\Enums\VariantTypesEnum;
use App\Exceptions\StoreServiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\OfferRequest;
use App\Libraries\Response\Responder;
use App\Mail\Client\OfferRequestReceived;
use App\Mail\CompanyCustomer\OfferRequestSent;
use App\Models\Catalog\Product\Product;
use App\Models\Customer;
use App\Services\Store\ProductService;
use App\Services\Store\StoreService;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mail;
use Str;
use Validator;

class StoreController extends Controller
{
    //store detail
    public function storeDetail(string $customer_id): JsonResponse
    {
        try {
            return Responder::send_success(data: StoreService::init($customer_id)->storeDetail());
        } catch (\Exception $e) {
            return Responder::send_bad_request(msg: $e->getMessage());
        }
    }
    public function allProducts(string $customer_id): JsonResponse
    {
        $limit = request()->get('limit', 10);
        $page = request()->get('page', 1);
        $search = request()->get('search', '');
        $category = request()->get('category', '');
        try {
            return Responder::send_success(
                data: StoreService::init($customer_id)
                    ->productService()
                    ->getProducts($limit, $page, $search, $category)
            );
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    public function productDetail(string $customer_id, string $product_id): JsonResponse
    {
        try {
            return Responder::send_success(
                data: StoreService::init($customer_id)
                    ->productService()
                    ->getProduct($product_id)
            );
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    public function addToCart(string $customer_id, string $product_id, Request $request): JsonResponse
    {
        $quantity = $request->post('quantity', 1);
        $productVariants = Product::whereUuid($product_id)->first()->variants;
        $variants = Validator::make($request->all(), [
            'variants' => 'array',
            'variants.*.id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($productVariants) {
                    if (!$productVariants->contains('id', $value)) {
                        $fail('Invalid variant id');
                    }
                }
            ],
            'variants.*.type' => "required|string|in:" . implode(',', VariantTypesEnum::getValues()),
            //json
            'variants.*.value' => 'required',
            "dimension" => "nullable|array",
            "dimension.*.id" => "required_with:dimension|nullable",
            "dimension.*.value" => "required_with:dimension|string",
            "color" => "nullable|array",
            "color.*.id" => "required_with:color|nullable",
            "color.*.value" => "required_with:color|string",
        ]);
        if ($variants->errors()->any()) {
            return Responder::send_bad_request(msg: $variants->errors()->first());
        }

        try {
            $service = StoreService::init($customer_id)->cartService();
            $service->addCart(
                $product_id,
                $quantity,
                $variants->validated()['color'] ?? null,
                $variants->validated()['dimension'] ?? null
            );
            return Responder::send_success();
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                dd($e);
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                dd($e);
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    public function removeFromCart(string $customer_id, string $item_id): JsonResponse
    {
        try {
            $service = StoreService::init($customer_id)->cartService();
            $service->removeCart($item_id);
            return Responder::send_success();
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    //update cart item
    public function updateCart(string $customer_id, string $product_id, Request $request): JsonResponse
    {
        $quantity = $request->post('quantity', 1);
        try {
            $service = StoreService::init($customer_id)->cartService();
            $service->updateCart($product_id, $quantity);
            return Responder::send_success();
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    public function clearCart(string $customer_id): JsonResponse
    {
        try {
            $service = StoreService::init($customer_id)->cartService();
            $service->clearCart();
            return Responder::send_success();
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    public function getCart(string $customer_id): JsonResponse
    {
        try {
            return Responder::send_success(data: StoreService::init($customer_id)->cartService()->getCart());
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                dd($e);
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    public function createOffer(string $customer_id, OfferRequest $request): JsonResponse
    {
        try {
            $service = StoreService::init($customer_id);
            $offerService = $service->offerService();
            $offer = $offerService->setFormData($request->validated())
                ->createOffer();


            $mail = (new OfferRequestReceived($offer))->onQueue('mail_job');
            Mail::to($offer->customer->email)->queue($mail);
            $mail2 = (new OfferRequestSent($offer, app()->getLocale()))->onQueue('mail_job');
            Mail::to($offer->companyCustomer->email)->locale(app()->getLocale())->queue($mail2);
            $service->cartService()->clearCart();
            return Responder::send_success();
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                dd($e);
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                dd($e);
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }

    //categories
    public function categories(string $customer_id): JsonResponse
    {
        try {
            $search = request()->get('search', '');
            return Responder::send_success(data: StoreService::init($customer_id)->productService()->getCategories($search));
        } catch (\Exception $e) {
            if ($e instanceof StoreServiceException) {
                return Responder::send_bad_request(msg: $e->getMessage());
            } else {
                return Responder::send_bad_request(msg: 'Error Occurred');
            }
        }
    }
}
