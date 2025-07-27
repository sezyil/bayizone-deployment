<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Libraries\Response\Responder;
use App\Mail\System\CompanyCustomerForgotPassword;
use App\Mail\System\CompanyCustomerResetPassword;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\CompanyCustomer\CompanyCustomerUser;
use App\Models\Customer;
use Auth;
use DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Mail;
use Password;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            //code is parent customer code
            'code' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'code.required' => 'Firma kodu boş bırakılamaz!',
            'email.required' => 'E-posta adresi boş bırakılamaz!',
            'email.email' => 'E-posta adresi geçersiz!',
            'password.required' => 'Şifre boş bırakılamaz!',
        ]);

        if ($validation->fails()) {
            return Responder::send_unprocessable("", $validation->errors());
        }
        $customer_id = null;
        //custom login
        $customer = CompanyCustomer::where('code', $input['code'])->first();
        if (!$customer) {
            return Responder::send_unprocessable("Firma kodu bulunamadı!");
        } else {
            $customer_id = $customer->id;
        }
        if (Auth::guard('dealer')->attempt([
            'email' => $input['email'],
            'password' => $input['password'],
            'company_customer_id' => $customer_id,
        ])) {
            /** @var CompanyCustomerUser $user */
            $user = Auth::guard('dealer')->user();
            $errors = [];
            $customerData = $user->companyCustomer;
            if ((bool)$customerData->status !== true) {
                return Responder::send_unprocessable("Firma hesabınız aktif değil!");
            };
            if ((bool)$user->status !== true) {
                return Responder::send_unprocessable("Kullanıcı hesabınız aktif değil!");
            };
            if (!$customerData->customer->activeSubscription) {
                return Responder::send_unprocessable("Firma aboneliğiniz bulunmamaktadır!");
            }

            return Responder::send_success("", $user->getAuthData(true));
        } else {
            return Responder::send_unprocessable("Giriş Başarısız");
        }
    }

    //Route::get('getInfo', 'userData')->name('userData');
    public function userData()
    {
        /** @var CompanyCustomerUser $user */
        $user = Auth::user();
        return Responder::send_success("", $user->getAuthData());
    }

    public function logout()
    {
        /** @var CompanyCustomerUser $user */
        $user = Auth::user();
        $user->tokens()->delete();
        return Responder::send_success("Çıkış Başarılı!");
    }

    /**
     * For reset password email send
     *
     * @param Request $request
     */
    public function forgotPassword(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'email' => [
                'code' => 'required',
                'required', 'email', 'exists:company_customer_users,email',
            ],
        ], [
            'email.required' => 'E-posta adresi boş bırakılamaz!',
            'email.email' => 'E-posta adresi geçersiz!',
            'email.exists' => 'E-posta adresi bulunamadı!',
            'code.required' => 'Firma kodu boş bırakılamaz!',
        ]);

        if ($validation->fails()) {
            return Responder::send_unprocessable("", $validation->errors());
        }

        $companyCustomer = CompanyCustomer::where('code', $input['code'])->first();
        if (!$companyCustomer) {
            return Responder::send_unprocessable("Firma kodu bulunamadı!");
        }

        //find in customer company customer's users
        $user = CompanyCustomerUser::where('email', $input['email'])->where('company_customer_id', $companyCustomer->id)->first();

        if ($user) {
            //check before token requested look email and created_at less than config expire time
            $token = DB::table('password_reset_tokens')
                ->where('email', $input['email'])
                ->where('code', $input['code'])
                ->first();
            if ($token) {
                $expireDate = config('auth.passwords.users.expire');
                $tokenDate = date('Y-m-d H:i:s', strtotime($token->created_at . ' +' . $expireDate . ' minutes'));
                if ($tokenDate > date('Y-m-d H:i:s')) {
                    return Responder::send_unprocessable("Şifre sıfırlama bağlantısı daha önce gönderilmiş. Lütfen e-posta adresinizi kontrol ediniz!");
                } else {
                    DB::table('password_reset_tokens')->where('email', $input['email'])->where('code', $input['code'])->delete();
                }
            }
            Mail::to($user)->send(new CompanyCustomerForgotPassword($user));
            return Responder::send_success("Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.");
        } else {
            return Responder::send_unprocessable("Girilen bilgilere ait kullanıcı bulunamadı!");
        }
    }

    /**
     * For reset password
     *
     * @param Request $request
     */
    public function resetPassword(Request $request)
    {
        $input = $request->all();
        //need user,token,password,password_confirmation
        $validation = Validator::make($input, [
            'user' => 'required|exists:company_customer_users,id',
            'token' => 'required',
            'password' => 'required|min:8|string',
            'password_confirmation' => 'required|same:password',
        ], [
            'user.required' => 'Kullanıcı bilgisi boş bırakılamaz!',
            'user.exists' => 'Kullanıcı bilgisi bulunamadı!',
            'token.required' => 'Token bilgisi boş bırakılamaz!',
            'password.required' => 'Şifre boş bırakılamaz!',
            'password.min' => 'Şifre en az 8 karakter olmalıdır!',
            'password.string' => 'Şifre geçersiz!',
            'password_confirmation.required' => 'Şifre tekrarı boş bırakılamaz!',
            'password_confirmation.same' => 'Şifre tekrarı eşleşmiyor!',
        ]);

        if ($validation->fails()) {
            return Responder::send_unprocessable("", $validation->errors());
        }

        $user = CompanyCustomerUser::where('id', $input['user'])->first();
        $credentials  = [
            'email' => $user->email,
            'password' => $input['password'],
            'company_customer_id' => $user->company_customer_id,
            'password_confirmation' => $input['password_confirmation'],
            'token' => $input['token']
        ];
        //set password guard
        config(['auth.defaults.password' => 'dealer']);
        $status = Password::broker('dealer')->reset($credentials, function (CompanyCustomerUser $dealer, $password) {
            $dealer->forceFill([
                'password' => bcrypt($password)
            ])->save();
            event(new PasswordReset($dealer));
        });
        if ($status === Password::PASSWORD_RESET) {
            Mail::to($user)->send(new CompanyCustomerResetPassword($user));
            return Responder::send_success("Şifre sıfırlama işlemi başarılı!");
        } else if ($status === Password::INVALID_TOKEN) {
            return Responder::send_unprocessable("Şifre sıfırlama bağlantısı geçersiz!");
        } else if ($status === Password::INVALID_USER) {
            return Responder::send_unprocessable("Kullanıcı bilgisi bulunamadı!");
        } else {
            return Responder::send_unprocessable("Şifre sıfırlama işlemi başarısız!");
        }
    }
}
