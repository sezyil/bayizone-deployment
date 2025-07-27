<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Libraries\Permissions\PermissionGenerator;
use App\Libraries\Response\Responder;
use App\Mail\System\CustomerForgotPassword;
use App\Mail\System\CustomerRegistered;
use App\Mail\System\CustomerResetPassword;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mail;
use Password;
use Response;
use Str;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $input = $request->all();

        $validation = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'E-posta adresi boş bırakılamaz!',
            'email.email' => 'E-posta adresi geçersiz!',
            'password.required' => 'Şifre boş bırakılamaz!',
        ]);

        if ($validation->fails()) {
            return Responder::send_unprocessable("", $validation->errors());
        }

        if (Auth::guard('user')->attempt(['email' => $input['email'], 'password' => $input['password']])) {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $errors = [];
            $customerData = $user->customer;
            if ((bool)$customerData->status !== true) $errors['customer_status'] = false;
            if ((bool)$user->status !== true) $errors['user_status'] = false;

            if ($errors) {
                return Responder::send_unprocessable("Giriş Başarısız", $errors);
            } else {
                if (!$user->email_verified_at) {
                    return Responder::send_unprocessable("Mail adresiniz doğrulanmamıştır.Lütfen e-posta adresinizi kontrol ediniz!");
                }
                return Responder::send_success("", $user->getAuthData(true));
            }
        } else {
            return Responder::send_unprocessable("Bu bilgilere ait kullanıcı bulunamadı!");
        }
    }

    public function register(Request $request)
    {
        $msg = "Hatalı Bilgiler!";
        $errors = [];
        $input = $request->all();
        //validator
        $validator = Validator::make($input, [
            "fullname" => "required",
            /* "firm_name" => "required|unique:App\Models\Customer,firm_name",
            "tax_no" => "required|unique:App\Models\Customer,tax_no|numeric|digits_between:10,11", */
            "email" => "required|email|unique:App\Models\User,email",
            "password" => "required|min:8|string",
            "terms" => [
                "required",
                function ($attribute, $value, $fail) {
                    if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
                        if ((bool)$value === false) $fail("Kullanım Koşulları ve Gizlilik Politikası kabul edilmelidir!");
                    } else {
                        $fail("Kullanım Koşulları ve Gizlilik Politikası kabul edilmelidir!");
                    }
                }
            ],
            "phone" => "required|numeric|digits_between:10,11",
        ], [
            "fullname.required" => "Ad Soyad boş bırakılamaz!",
            /* "firm_name.required" => "Firma Adı boş bırakılamaz!",
            "firm_name.unique" => "Firma Adı daha önce kullanılmış!",
            "tax_no.required" => "Vergi Numarası boş bırakılamaz!",
            "tax_no.unique" => "Vergi Numarası daha önce kullanılmış!",
            "tax_no.numeric" => "Vergi Numarası sadece rakamlardan oluşmalıdır!",
            "tax_no.digits_between" => "Vergi Numarası 10-11 haneli olmalıdır!", */
            "email.required" => "E-posta adresi boş bırakılamaz!",
            "email.email" => "E-posta adresi geçersiz!",
            "email.unique" => "E-posta adresi daha önce kullanılmış!",
            "password.required" => "Şifre boş bırakılamaz!",
            "password.min" => "Şifre en az 8 karakter olmalıdır!",
            "terms.required" => "Kullanım Koşulları ve Gizlilik Politikası kabul edilmelidir!",
            "phone.required" => "Telefon Numarası boş bırakılamaz!",
            "phone.numeric" => "Telefon Numarası sadece rakamlardan oluşmalıdır!",
            "phone.digits_between" => "Telefon Numarası 10-11 haneli olmalıdır!",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable($msg, $errors);
        } else {
            $input['password'] = bcrypt($input['password']);
            $customer = Customer::create([
                'authorized_person' => $input['fullname'],
                'email' => $input['email'],
                'secret_key' => md5(Str::random(15) . time()),
                'app_key' => md5(Str::random(15) . time()),
                'phone' => $input['phone'],
            ]);
            setPermissionsTeamId($customer->id);
            $permission_id = PermissionGenerator::generateForNewRegister($customer->id);

            $user = User::create([
                'customer_id' => $customer->id,
                'fullname' => $input['fullname'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role_id' => $permission_id->id,
                'is_super_user' => true,
                'phone' => $input['phone'],
            ]);

            $user->assignRole($permission_id);
            $mail = (new CustomerRegistered($user))->onQueue('mail_job');
            Mail::to($user)->send($mail);
            $msg = "Kayıt Başarılı!";
            return Responder::send_success($msg);
        }
    }

    //Route::get('getInfo', 'userData')->name('userData');
    public function userData()
    {
        /** @var User $user */
        $user = Auth::user();
        return Responder::send_success("", $user->getAuthData());
    }

    public function logout()
    {
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
                'required', 'email', 'exists:App\Models\User,email',
            ],
        ], [
            'email.required' => 'E-posta adresi boş bırakılamaz!',
            'email.email' => 'E-posta adresi geçersiz!',
            'email.exists' => 'E-posta adresi bulunamadı!',
        ]);

        if ($validation->fails()) {
            return Responder::send_unprocessable("", $validation->errors());
        }

        $user = User::where('email', $input['email'])->first();
        if ($user) {
            //check before token requested look email and created_at less than config expire time
            $token = DB::table('password_reset_tokens')->where('email', $input['email'])->first();
            if ($token) {
                $expireDate = config('auth.passwords.users.expire');
                $tokenDate = date('Y-m-d H:i:s', strtotime($token->created_at . ' +' . $expireDate . ' minutes'));
                if ($tokenDate > date('Y-m-d H:i:s')) {
                    return Responder::send_unprocessable("Şifre sıfırlama bağlantısı daha önce gönderilmiş. Lütfen e-posta adresinizi kontrol ediniz!");
                } else {
                    DB::table('password_reset_tokens')->where('email', $input['email'])->delete();
                }
            }
            $mail = (new CustomerForgotPassword($user))->onQueue('mail_job');
            Mail::to($user)->queue($mail);
            return Responder::send_success("Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.");
        } else {
            return Responder::send_unprocessable("E-posta adresi bulunamadı!");
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
            'user' => 'required|exists:App\Models\User,id',
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

        $user = User::where('id', $input['user'])->first();
        $credentials  = [
            'email' => $user->email,
            'password' => $input['password'],
            'password_confirmation' => $input['password_confirmation'],
            'token' => $input['token']
        ];
        $status = Password::reset($credentials, function (User $user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
            event(new PasswordReset($user));
        });
        if ($status === Password::PASSWORD_RESET) {
            $mail = (new CustomerResetPassword($user))->onQueue('mail_job');
            Mail::to($user)->queue($mail);
            return Responder::send_success("Şifre sıfırlama işlemi başarılı!");
        } else if ($status === Password::INVALID_TOKEN) {
            return Responder::send_unprocessable("Şifre sıfırlama bağlantısı geçersiz!");
        } else if ($status === Password::INVALID_USER) {
            return Responder::send_unprocessable("Kullanıcı bilgisi bulunamadı!");
        } else {
            return Responder::send_unprocessable("Şifre sıfırlama işlemi başarısız!");
        }
    }

    //email verify
    public function verifyEmail($userId)
    {
        $user = User::whereId($userId)->first();
        if ($user) {
            if ($user->email_verified_at) {
                return '<script>alert("E-posta adresiniz zaten doğrulanmış.");window.location.href="https://app.bayizone.com";</script>';
            }
            $user->email_verified_at = now();
            $user->save();

            $msg = "E-posta adresiniz başarıyla doğrulandı";
            return '<script>alert("' . $msg . '");window.location.href="https://app.bayizone.com";</script>';
        } else {
            return '<script>alert("E-posta adresiniz doğrulanamadı.");window.location.href="https://app.bayizone.com";</script>';
        }
    }
}
