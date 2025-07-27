<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerBankAllListCollection;
use App\Http\Resources\Client\CompanyCustomer\CompanyCustomerBankDetailCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomerBankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_customer_id = SanctumDealerHelper::company_customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_customer_id)
            ->get();
        $result = new CompanyCustomerBankAllListCollection($data);
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
    public function store(Request $request): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_customer_id = SanctumDealerHelper::company_customer_id();
        $input = $request->all();
        $validator = validator($input, [
            'bank_name' => 'required|string',
            'branch_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'iban' => 'required|string',
            'swift_code' => 'nullable|string',
            'currency' => [
                'required',
                Rule::exists('currencies', 'code')->where(function ($query) {
                    return $query->where('status', 1);
                })
            ],
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("Hatalı veri girişi", $validator->errors());
        } else {
            $data = $validator->validated();

            //create bank account
            $data['customer_id'] = $customer_id;
            $data['company_customer_id'] = $company_customer_id;
            CompanyCustomerBankAccount::create($data);


            return Responder::send_success("Banka hesabı eklendi");
        }
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
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->where('id', $id)->firstOrFail();
        $response = new CompanyCustomerBankDetailCollection([$data]);
        return Responder::send_success("", $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $input = $request->all();
        $model = CompanyCustomerBankAccount::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_id)
            ->where('id', $id)
            ->firstOrFail();
        $table = 'company_customer_bank_accounts';
        $uniq = Rule::unique($table)->where(function ($query) use ($company_id) {
            return $query->where('company_customer_id', $company_id);
        })->ignore($id);
        $validator = validator($input, [
            'bank_name' => 'required|string',
            'branch_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'iban' => [
                'required',
                $uniq,
            ],
            'swift_code' => [
                'nullable',
                $input['swift_code'] ? $uniq : ''
            ],
            'currency' => [
                'required',
                Rule::exists('currencies', 'code')->where(function ($query) {
                    return $query->where('status', 1);
                })
            ],
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("Hatalı veri girişi", $validator->errors());
        } else {
            $data = $validator->validated();
            $model->update($data);
            return Responder::send_success("Banka hesabı güncellendi");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $data = CompanyCustomerBankAccount::where('customer_id', $customer_id)
            ->where('company_customer_id', $company_id)
            ->where('id', $id)->firstOrFail();
        $data->delete();
        return Responder::send_success("Banka hesabı silindi");
    }
}
