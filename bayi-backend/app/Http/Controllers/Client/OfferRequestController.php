<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer;
use App\Models\OfferRequest;
use App\Models\OfferRequestLine;
use App\Models\OfferRequestLineVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OfferRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customer_id = SanctumHelper::customer_id();

        $total = 0;
        $limit = DatatableResponder::getLimit();
        $current_page = DatatableResponder::getCurrentPage();


        $data = OfferRequest::where('customer_id', $customer_id)->with('lines')->paginate($limit, ['*'], 'page', $current_page);
        $total = $data->total();
        $data = collect($data->items())->transform(function ($item) {
            /** @var OfferRequest $item */
            /** @var OfferRequestLine $lines */
            $lines = $item->lines();
            return [
                'id' => $item->id,
                'company_id' => $item->companyCustomer->id,
                'company_name' => $item->companyCustomer->company_name,
                'offer_id' => $item->customer_offer_id,
                'global_note' => $item->global_note,
                'from_store' => $item->from_store,
                'currency' => $item->currencyRelation->title,
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                'item_count' => $lines->count(),
                'status' => $item->status,
            ];
        });
        return DatatableResponder::sendResponse($data, $total);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $customer_id = SanctumHelper::customer_id();
        $offer = OfferRequest::where('id', $id)
            ->where('customer_id', $customer_id)
            ->with('lines')
            ->firstOrFail();
        $offer->lines = $offer->lines->transform(function ($item) {
            /** @var OfferRequestLine $item */
            return [
                'id' => $item->id,
                'product_id' => $item->product->id,
                'product_name' => $item->product->description->name,
                'default_price' => 0,
                'image' => $item->product->image,
                'model' => $item->product->model,
                'quantity' => $item->quantity,
                'note' => $item->note,
                'color' => $item->variants()->colors()->get()->map(function ($variant) {
                    /** @var OfferRequestLineVariant $variant */
                    return $variant->transformData(true);
                }) ?? null,
                'dimension' => $item->variants()->dimensions()->get()->map(function ($variant) {
                    /** @var OfferRequestLineVariant $variant */
                    return $variant->transformData(true);
                }) ?? null,
            ];
        });
        $customer = Customer::where('id', $customer_id)->first();
        $companyCustomer = CompanyCustomer::where('id', $offer->company_customer_id)->first();
        $response = [
            'offer' => $offer,
            'bank_accounts' => $customer->customerBankAccounts()->get(),
            'company_customer' => $companyCustomer,
            'cc_warehouses' => $companyCustomer->warehouses()->get(),
        ];
        return Responder::send_success("", $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }
}
