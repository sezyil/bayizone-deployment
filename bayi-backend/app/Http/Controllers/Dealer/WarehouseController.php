<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CompanyCustomer\CompanyCustomerWarehouseRequest;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerWarehouseDetailCollection;
use App\Http\Resources\Client\Customer\CompanyCustomerWarehouseListCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\CompanyCustomer\CompanyCustomerWarehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $company_customer_id = SanctumDealerHelper::company_customer_id();
        $customer_warehouses = CompanyCustomerWarehouse::where('company_customer_id', $company_customer_id)->get();
        $result = new CompanyCustomerWarehouseListCollection($customer_warehouses);
        return DatatableResponder::sendResponse($result, $result->count());
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
    public function store(CompanyCustomerWarehouseRequest $request): JsonResponse
    {
        $company_customer_id = SanctumDealerHelper::company_customer_id();
        $customer_id = SanctumDealerHelper::customer_id();
        $data = $request->validated();
        $data['customer_id'] = $customer_id;
        $data['company_customer_id'] = $company_customer_id;

        CompanyCustomerWarehouse::create($data);
        return Responder::send_success("");
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
    public function edit(string $warehouse_id): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $warehouse = CompanyCustomerWarehouse::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_id)
            ->where('id', $warehouse_id)
            ->with('companyCustomer')->firstOrFail();

        $result = $warehouse->get()->transform(fn($data)=>[
            "id" => $data->id,
            "name"  => $data->name,
            "address"   => $data->address,
            "phone" => $data->phone,
            "email" => $data->email,
            "contact_person"    => $data->contact_person,
            "country_id"   => $data->country_id,
            "city_id"  => $data->city_id,
            "state_id" => $data->state_id,
            "contact_person_phone"  => $data->contact_person_phone,
            "contact_person_email"  => $data->contact_person_email,
            "zip_code"  => $data->zip_code,
        ])->first();
        return Responder::send_success("", $result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyCustomerWarehouseRequest $request, string $warehouse): JsonResponse
    {
        $company_customer_id = SanctumDealerHelper::company_customer_id();
        $customer_id = SanctumDealerHelper::customer_id();
        $warehouse = CompanyCustomerWarehouse::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer_id)
            ->where('id', $warehouse)->firstOrFail();
        $data = $request->validated();
        $warehouse->update($data);
        return Responder::send_success("");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $warehouse = CompanyCustomerWarehouse::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $warehouse->delete();
        return Responder::send_success("");
    }
}
