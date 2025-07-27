<?php

namespace App\Http\Controllers\Client\Customer;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BankAccountRequest;
use App\Http\Resources\Client\Customer\CustomerBankAccountDetailCollection;
use App\Http\Resources\Client\Customer\CustomerBankAccountListCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CustomerBankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomerBankAccountController extends Controller
{
    private $permissionClass = PermissionTypes::customer_bank_account;
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
        //if currency_code query param is set, filter by currency code
        $currency_code = request()->get('currency_code');
        $company_customers = CustomerBankAccount::where('customer_id', $customer_id)->active();
        if ($currency_code) {
            $company_customers = $company_customers->where('currency', $currency_code);
        }

        $total = 0;
        $limit = DatatableResponder::getLimit(true);
        $current_page = DatatableResponder::getCurrentPage(true);
        if ($limit && $current_page) {
            $company_customers = $company_customers->paginate($limit, ['*'], 'page', $current_page);
            $total = $company_customers->total();
            $company_customers = $company_customers->items();
        } else {
            $company_customers = $company_customers->get();
            $total = $company_customers->count();
        }

        $collection = new CustomerBankAccountListCollection($company_customers);
        return DatatableResponder::sendResponse($collection, $total);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BankAccountRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->all();
        CustomerBankAccount::create($input + ['customer_id' => $customer_id]);
        return Responder::send_success("");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $bankAccount = CustomerBankAccount::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $response = new CustomerBankAccountDetailCollection([$bankAccount]);
        return Responder::send_success("", $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BankAccountRequest $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->all();
        /** @var CustomerBankAccount $bankAccount */
        $bankAccount = CustomerBankAccount::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $bankAccount->update($input);
        return Responder::send_success("");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $bankAccount = CustomerBankAccount::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $bankAccount->delete();
        return Responder::send_success("");
    }
}
