<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomerUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $company_id = SanctumDealerHelper::company_customer_id();
        $data = CompanyCustomerUser::where('company_customer_id', $company_id)
            ->where('is_main_user', false)
            ->get()
            ->transform(function ($item) {
                /** @var CompanyCustomerUser $item */
                unset($item->customer_id, $item->created_at, $item->updated_at);
                return $item;
            });
        return DatatableResponder::sendResponse($data, $data->count());
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
        $msg = "Kayıt başarısız!";
        $errors = [];
        $input = $request->all();
        //validator
        $validator = Validator::make($input, [
            "fullname" => "required",
            "email" => "required|email|unique:company_customer_users,email",
            "password" => "required|min:8",
            "phone" => "required|max:20",
            "repass" => "required|same:password",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable($msg, $errors);
        } else {
            $input['password'] = bcrypt(trim($input['password']));

            CompanyCustomerUser::create([
                'customer_id' => SanctumDealerHelper::customer_id(),
                'company_customer_id' => SanctumDealerHelper::company_customer_id(),
                'fullname' => trim($input['fullname']),
                'email' => trim($input['email']),
                'password' => $input['password'],
                'phone' => trim($input['phone']),
            ]);

            $msg = "Kayıt başarılı!";
            return Responder::send_success($msg);
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
        $user = CompanyCustomerUser::where('id', $id)
            ->where('is_main_user', false)
            ->where('company_customer_id', SanctumDealerHelper::company_customer_id())
            ->firstOrFail();
        $user = $user->only(['id', 'fullname', 'email', 'phone']);
        return Responder::send_success("", $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = CompanyCustomerUser::where('id', $id)
            ->where('is_main_user', false)
            ->where('company_customer_id', SanctumDealerHelper::company_customer_id())
            ->firstOrFail();

        $input = $request->all();
        $validator = Validator::make($input, [
            "fullname" => "required",

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {
            $user->fullname = $input['fullname'];
            $user->phone = $input['phone'];
            $user->save();
            return Responder::send_success("Kullanıcı güncellendi!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = CompanyCustomerUser::where('id', $id)->firstOrFail();
        $user->delete();
        return Responder::send_success("Kullanıcı silindi!");
    }
}
