<?php

namespace App\Http\Controllers\Dealer\OfferRequest;

use App\Enums\OfferRequestStatusEnum;
use App\Enums\VariantTypesEnum;
use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariantValue;
use App\Models\OfferRequest;
use App\Models\OfferRequestLine;
use App\Models\OfferRequestLineVariant;
use App\Models\System\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class OfferRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $data = OfferRequest::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->with('lines')->orderByDesc('created_at')->get()->transform(function ($item) {
            /** @var OfferRequest $item */
            /** @var OfferRequestLine $lines */
            $lines = $item->lines();
            $firstItemName = $lines->first()->product->description->name ?? '';
            return [
                'id' => $item->id,
                'global_note' => $item->global_note,
                'currency' => $item->currencyRelation->title,
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                //is new if 1 day old
                'is_new' => $item->created_at->diffInDays() < 1,
                'item_count' => $lines->count(),
                'status' => $item->status,
            ];
        });
        return DatatableResponder::sendResponse($data, $data->count());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $input = $request->all();
        $validator = Validator::make($input, [
            'global_note' => 'nullable|string|max:255',
            'currency' => 'required|string|in:' . Currency::all()->pluck('code')->implode(','),
            'products' => 'required|array',
            'products.*.id' => [
                'required',
                'string',
                'exists:products,uuid',
                function ($attribute, $value, $fail) use ($customer_id) {
                    $product = Product::where('uuid', $value)->first();
                    /** @var Product $product */
                    if ($product->customer_id != $customer_id) {
                        $fail('Ürün Bulunamadı');
                    }
                },
            ],
            'products.*.quantity' => 'required|integer|min:1|max:999',
            'products.*.note' => 'nullable|string|max:500',
            "products.*.color" => "nullable|array",
            "products.*.color.*.product_variant_id" => "required_with:products.*.color|string",
            "products.*.color.*.value_id" => "required_with:products.*.color|string",
            "products.*.color.*.variant_id" => "required_with:products.*.color|string",
            "products.*.dimension" => "nullable|array",
            "products.*.dimension.*.product_variant_id" => "required_with:products.*.dimension|string",
            "products.*.dimension.*.value_id" => "required_with:products.*.dimension|string",
            "products.*.dimension.*.variant_id" => "required_with:products.*.dimension|string",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        }
        $input = $validator->validated();
        $offer = OfferRequest::create([
            'customer_id' => $customer_id,
            'company_customer_id' => $company_id,
            'currency' => $input['currency'],
            'global_note' => $input['global_note'] ?? null,
        ]);

        foreach ($input['products'] as $product) {
            $note = $product['note'] ?? null;
            /** @var OfferRequestLine $line */
            $line = $offer->lines()->create([
                'product_id' => Product::where('uuid', $product['id'])->first()->id,
                'quantity' => $product['quantity'],
                'note' => $note
            ]);

            $color = isset($product['color']) && is_array($product['color']) ? $product['color'] : [];
            if ($color) {
                foreach ($color as $colorItem) {
                    $productVariantValue = ProductVariantValue::where('product_variant_id', $colorItem['product_variant_id'])
                        ->where('id', $colorItem['value_id'])
                        ->first();
                    if ($productVariantValue) {
                        $line->variants()->create([
                            'type' => VariantTypesEnum::COLOR->value,
                            'product_variant_id' => $colorItem['product_variant_id'],
                            'product_variant_value_id' => $productVariantValue->id,
                        ]);
                    }
                }
            }

            $dimension = isset($product['dimension']) && is_array($product['dimension']) ? $product['dimension'] : [];
            if ($dimension) {
                foreach ($dimension as $dimensionItem) {
                    $productVariantValue = ProductVariantValue::where('product_variant_id', $dimensionItem['product_variant_id'])
                        ->where('id', $dimensionItem['value_id'])
                        ->first();
                    if ($productVariantValue) {
                        $line->variants()->create([
                            'type' => VariantTypesEnum::DIMENSION->value,
                            'product_variant_id' => $dimensionItem['product_variant_id'],
                            'product_variant_value_id' => $productVariantValue->id,
                        ]);
                    }
                }
            }
        }

        return Responder::send_success("");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $offer = OfferRequest::select(['id', 'global_note', 'created_at'])
            ->where('id', $id)
            ->where('customer_id', $customer_id)
            ->where('company_customer_id', $company_id)
            ->with('lines')
            ->firstOrFail();
        $offer->lines = $offer->lines->transform(function ($item) {
            /** @var OfferRequestLine $item */
            return [
                'id' => $item->id,
                'product_name' => $item->product->description?->name ?? $item->product->descriptions()->first()->name,
                'image' => $item->product->image,
                'model' => $item->product->model,
                'quantity' => $item->quantity,
                'color' => $item->variants()->colors()->get()->map(function ($variant) {
                    /** @var OfferRequestLineVariant $variant */
                    return $variant->transformData(true, true);
                }) ?? null,
                'dimension' => $item->variants()->dimensions()->get()->map(function ($variant) {
                    /** @var OfferRequestLineVariant $variant */
                    return $variant->transformData(true, true);
                }) ?? null,
                'note' => $item->note,
            ];
        });
        return Responder::send_success("", $offer);
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
    public function destroy(string $id): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $offer = OfferRequest::where('id', $id)
            ->where('customer_id', $customer_id)
            ->where('company_customer_id', $company_id)
            ->where('status', OfferRequestStatusEnum::PENDING->value)
            ->firstOrFail();
        $offer->lines()->delete();
        $offer->delete();
        return Responder::send_success("");
    }
}
