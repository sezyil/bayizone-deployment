<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProfileUpdateRequest;
use App\Http\Resources\Client\ProfileCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomerUser;
use App\Models\System\Language;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //get user id and customer id
        $user_id = SanctumDealerHelper::user_id();
        $user = CompanyCustomerUser::where('id', $user_id)->first();
        $data = new ProfileCollection($user);
        return Responder::send_success("", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user_id = SanctumDealerHelper::user_id();
        $input = $request->all();
        $user = CompanyCustomerUser::where('id', $user_id)->first();
        $user->fullname = $input['fullname'];
        if ($input['password'] != null && $input['password'] != "") {
            $user->password = bcrypt($input['password']);
        }
        $user->save();
        return Responder::send_success("Profil bilgileri güncellendi.");
    }

    //set language
    public function language(Request $request): JsonResponse
    {
        $user_id = SanctumDealerHelper::user_id();
        $input = $request->all();
        $user = CompanyCustomerUser::where('id', $user_id)->first();
        $user->language = Language::where('code', $input['language'])->first()?->code ?? 'tr';
        $user->save();
        return Responder::send_success("Dil ayarları güncellendi.");
    }
}
