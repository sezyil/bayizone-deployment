<?php

namespace App\Http\Controllers\Client\CompanyCustomer;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CompanyCustomer\CompanyCustomerWarehouseRequest;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerWarehouseDetailCollection;
use App\Http\Resources\Client\Customer\CompanyCustomerWarehouseListCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\HttpStatusCodes;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\CompanyCustomer\CompanyCustomerWarehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyCustomerWarehouseController extends Controller
{
    private $permissionClass = PermissionTypes::company_customer_warehouse;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::customer_offer),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::customer_offer),
        ]);
        $customer_id = SanctumHelper::customer_id();
        $company_customer_id = request()->get('company_customer_id');
        $customer_warehouses = CompanyCustomerWarehouse::whereCustomerId($customer_id)
            ->when($company_customer_id, fn ($query) => $query->whereCompanyCustomerId($company_customer_id))
            ->get();
        $result = new CompanyCustomerWarehouseListCollection($customer_warehouses);
        return DatatableResponder::sendResponse($result, $result->count());
    }
    /**
     * Display a listing of the resource.
     */
    public function list(string $company_customer_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::customer_offer),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::customer_offer),
        ]);
        $customer_id = SanctumHelper::customer_id();
        //check company customer id
        CompanyCustomer::where('customer_id', $customer_id)->where('id', $company_customer_id)->firstOrFail();

        $customer_warehouses = CompanyCustomerWarehouse::where('customer_id', $customer_id)->where('company_customer_id', $company_customer_id)->get();
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
    public function store(CompanyCustomerWarehouseRequest $request, string $company_customer_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //check company customer id
        CompanyCustomer::where('customer_id', $customer_id)->where('id', $company_customer_id)->firstOrFail();

        $data = $request->validated();
        $data['customer_id'] = $customer_id;
        $data['company_customer_id'] = $company_customer_id;

        CompanyCustomerWarehouse::create($data);
        return Responder::send_success("");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $company_id, string $warehouse_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $warehouse = CompanyCustomerWarehouse::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_id)
            ->where('id', $warehouse_id)
            ->with('companyCustomer')->get();
        if ($warehouse->count() == 0) {
            return Responder::send_response(false, "", HttpStatusCodes::HTTP_NOT_FOUND);
        }
        $result = new CompanyCustomerWarehouseDetailCollection($warehouse);
        return Responder::send_success("", $result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyCustomerWarehouseRequest $request, string $company_customer_id, int $warehouse): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
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
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $warehouse = CompanyCustomerWarehouse::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $warehouse->delete();
        return Responder::send_success("");
    }
}
