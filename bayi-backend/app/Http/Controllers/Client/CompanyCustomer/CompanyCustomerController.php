<?php

namespace App\Http\Controllers\Client\CompanyCustomer;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CompanyCustomer\CompanyCustomerRequest;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerListCollection;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerDetailCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Mail\CompanyCustomer\CompanyCustomerCreated;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\CompanyCustomer\CompanyCustomerUser;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Mail;
use Str;

class CompanyCustomerController extends Controller
{
    private $permissionClass = PermissionTypes::company_customer;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::VIEW->generatePermission(PermissionTypes::customer_offer),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::company_customer_bank_account),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::company_customer_warehouse),
        ]);
        $customer_id = SanctumHelper::customer_id();
        $limit = request()->get('limit', 10);
        $current_page = request()->get('current_page', 1);
        $filter_country = request()->get('country');
        $filter_email = request()->get('customer_email');
        $filter_name = request()->get('customer_name');

        $company_customers = CompanyCustomer::where('customer_id', $customer_id)
            ->when($filter_country, function ($query) use ($filter_country) {
                $query->where('country_id', $filter_country);
            })
            ->when($filter_email, function ($query) use ($filter_email) {
                $query->where('email', 'like', '%' . $filter_email . '%');
            })
            ->when($filter_name, function ($query) use ($filter_name) {
                $query->where('company_name', 'like', '%' . $filter_name . '%');
            })
            ->paginate($limit, ['*'], 'page', $current_page);
        $collection = new CompanyCustomerListCollection($company_customers);

        return DatatableResponder::sendResponse($collection, $company_customers->total());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyCustomerRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->validated();
        $input['city_id'] = (int) $input['city_id'] === 0 ? null : $input['city_id'];
        $input['state_id'] = (int) $input['state_id'] === 0 ? null : $input['state_id'];
        CompanyCustomer::create($input + ['customer_id' => $customer_id]);
        return Responder::send_success("");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::customer_offer),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::customer_offer),
        ]);
        $customer_id = SanctumHelper::customer_id();
        $company_customer = CompanyCustomer::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $collection = new CompanyCustomerDetailCollection([$company_customer]);
        return Responder::send_success("", $collection);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyCustomerRequest $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();

        $company_customer = CompanyCustomer::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();

        $input = $request->validated();

        if ($company_customer->type == 2) {
            $input['tax_office'] = null;
        }

        $input['city_id'] = (int) $input['city_id'] === 0 ? null : $input['city_id'];
        $input['state_id'] = (int) $input['state_id'] === 0 ? null : $input['state_id'];

        $company_customer->update($input);

        return Responder::send_success("");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $company_customer = CompanyCustomer::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $company_customer->delete();
        return Responder::send_success("");
    }

    //create customer user
    public function createCustomerUser(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, PermissionTypes::company_customer);

        /** @var Customer $customer */
        $customer = SanctumHelper::getCustomer();
        $customer_id = $customer->id;
        $company_customer = CompanyCustomer::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        $hasProviderCount = $customer->activeSubscription?->hasProviderPanelCount();
        if (!$hasProviderCount) {
            return Responder::send_unprocessable("Müşteri paneli kullanıcı oluşturmak için limitiniz dolmuştur.");
        }
        if ($company_customer->users()->count() > 0) {
            return Responder::send_unprocessable("Bu müşteriye ait bir kullanıcı zaten var.");
        }
        $randomPass = Str::random(12);
        $newUser = CompanyCustomerUser::create([
            'customer_id' => $customer_id,
            'company_customer_id' => $company_customer->id,
            'phone' => $company_customer->phone,
            'fullname' => $company_customer->authorized_name,
            'email' => $company_customer->email,
            'password' => bcrypt($randomPass),
            'password_need_change' => true,
            'is_main_user' => true
        ]);
        try {
            $sended = Mail::to($newUser)->locale($company_customer->language)->send(new CompanyCustomerCreated($newUser, $randomPass));
            $customer->activeSubscription?->decreaseProviderPanelCount(1);
        } catch (\Exception $e) {
            $newUser->delete();
            return Responder::send_unprocessable("Kullanıcı oluşturulurken bir hata oluştu.");
        }
        return Responder::send_success("");
    }
}
