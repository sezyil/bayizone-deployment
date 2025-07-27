<?php

namespace App\Http\Controllers\Client\CompanyCustomer;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CompanyCustomer\CustomerCompanyBankRequest;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerBankAllListCollection;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerBankDetailCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\CompanyCustomer\CompanyCustomerBankAccount;
use Illuminate\Http\JsonResponse;

class CompanyCustomerBankAccountController extends Controller
{
    private $permissionClass = PermissionTypes::company_customer_bank_account;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)->with('parentCompanyCustomer')->get();
        $result = new CompanyCustomerBankAllListCollection($data);
        return DatatableResponder::sendResponse($result, $result->count());
    }
    /**
     * Display a listing of the resource.
     */
    public function list(string $company_customer): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->with('parentCompanyCustomer')->get();
        $result = new CompanyCustomerBankAllListCollection($data);
        return DatatableResponder::sendResponse($result, $result->count());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerCompanyBankRequest $request, string $company_customer_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();

        //check company customer id
        CompanyCustomer::where('customer_id', $customer_id)->where('id', $company_customer_id)->firstOrFail();

        $data = $request->validated();

        //create bank account
        $data['customer_id'] = $customer_id;
        $data['company_customer_id'] = $company_customer_id;
        CompanyCustomerBankAccount::create($data);


        return Responder::send_success("Banka hesabı eklendi", []);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $company_id, string $bank_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->where('id', $bank_id)->first();
        $response = new CompanyCustomerBankDetailCollection([$data]);
        return Responder::send_success("", $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerCompanyBankRequest $request, string $company_customer_id, string $bank_account_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();

        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer_id)
            ->where('id', $bank_account_id)->firstOrFail();

        $data->update($request->validated());

        return Responder::send_success("Banka hesabı güncellendi", []);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company_customer_id, string $bank_account_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer_id)
            ->where('id', $bank_account_id)->firstOrFail();

        $data->delete();

        return Responder::send_success("Banka hesabı silindi", []);
    }
}
