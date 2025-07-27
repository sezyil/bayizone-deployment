<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProfileUpdateRequest;
use App\Http\Resources\Client\ProfileCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\Responder;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //get user id and customer id
        $user_id = SanctumHelper::user_id();
        $data = new ProfileCollection(User::find($user_id));
        return Responder::send_success("", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user_id = SanctumHelper::user_id();
        $input = $request->all();
        $user = User::find($user_id);
        $user->fullname = $input['fullname'];
        if ($input['password'] != null && $input['password'] != "") {
            $user->password = bcrypt($input['password']);
        }
        $user->save();
        return Responder::send_success("Profil bilgileri güncellendi.");
    }
}
