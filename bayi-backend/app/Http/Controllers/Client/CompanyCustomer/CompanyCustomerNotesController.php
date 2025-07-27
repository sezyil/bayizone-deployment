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
use App\Models\CompanyCustomer\CompanyCustomerNote;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;
use Validator;

class CompanyCustomerNotesController extends Controller
{
    private $permissionClass = PermissionTypes::company_customer;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $current_page = request()->get('current_page', 1);
        $limit = request()->get('limit', 10);
        $data = CompanyCustomerNote::where('customer_id', $customer_id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $current_page);
        $response = [];
        foreach ($data as $item) {
            $response[] = [
                'id' => $item->id,
                'customer_id' => $item->customer_id,
                'company_customer_id' => $item->company_customer_id,
                'note' => Str::limit($item->note, 50),
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                'updated_at' => $item->updated_at->format('d.m.Y H:i'),
                'company_customer_name' => $item->companyCustomer->company_name
            ];
        }
        return DatatableResponder::sendResponse($response, $data->total());
    }
    /**
     * Display a listing of the resource.
     */
    public function list(string $company_customer): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $current_page = request()->get('current_page', 1);
        $limit = request()->get('limit', 10);
        $date= request()->get('date', null);
        $data = CompanyCustomerNote::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer)
            ->when($date, function ($query) use ($date) {
                //if date is valid then filter by date use Carbon
                if(Carbon::createFromFormat('Y-m-d', $date)){
                    $query->whereDate('created_at', $date);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $current_page);
        $response = [];
        foreach ($data as $item) {

            $response[] = [
                'id' => $item->id,
                'customer_id' => $item->customer_id,
                'company_customer_id' => $item->company_customer_id,
                'note' => Str::limit($item->note, 50),
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                'updated_at' => $item->updated_at->format('d.m.Y H:i'),
                'company_customer_name' => $item->companyCustomer->company_name
            ];
        }
        return DatatableResponder::sendResponse($response, $data->total());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $company_customer_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();

        //check company customer id
        CompanyCustomer::where('customer_id', $customer_id)->where('id', $company_customer_id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'note' => 'required|string|max:500',
        ], [
            'note.required' => 'Not alanı zorunludur',
            'note.string' => 'Not alanı metin tipinde olmalıdır',
            'note.max' => 'Not alanı en fazla 500 karakter olabilir',
        ]);

        $data = $validator->validated();

        //create bank account
        $data['customer_id'] = $customer_id;
        $data['company_customer_id'] = $company_customer_id;
        CompanyCustomerNote::create($data);


        return Responder::send_success("Not eklendi", []);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $company_id, string $note): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CompanyCustomerNote::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->where('id', $note)->first();
        $response = $data->toArray();
        return Responder::send_success("", $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $company_customer_id, string $note): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();

        $data = CompanyCustomerNote::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer_id)
            ->where('id', $note)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'note' => 'required|string|max:500',
        ]);

        $updateVal = $validator->validated();

        $data->update($updateVal);

        return Responder::send_success("Banka hesabı güncellendi", []);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company_customer_id, string $note): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CompanyCustomerNote::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer_id)
            ->where('id', $note)->firstOrFail();

        $data->delete();

        return Responder::send_success("Banka hesabı silindi", []);
    }
}
