<?php

namespace App\Http\Controllers\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionRoleEnum;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Models\System\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class UsersController extends Controller
{
    private $permissionClass = PermissionTypes::user;
    public function index()
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = User::where('customer_id', $customer_id);
        $role = Role::where('name', 'administrator')->where('team_id', $customer_id)->first();

        if ($role) {
            $data = $data->where('role_id', '!=', $role->id);
        }

        $total = 0;
        $limit = DatatableResponder::getLimit();
        $current_page = DatatableResponder::getCurrentPage();

        $data = $data->with('roles')->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $current_page);
        $total = $data->total();
        $data = collect($data->items())->each(function (&$item) {
            $item->roles->makeHidden(['guard_name', 'team_id', 'created_at', 'updated_at', 'pivot']);
            $item->roles = $item->roles->first()->nameToView();
        })->map(function ($item) {
            $item->role = $item->roles;
            unset($item->roles);
            return $item;
        });

        return DatatableResponder::sendResponse($data, $total);
    }


    public function create()
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $roles = Role::where('team_id', SanctumHelper::customer_id())->where('name', '!=', PermissionRoleEnum::ADMIN->value)->get();
        $data = [
            'roles' => $roles->each(function ($item) {
                $item->name = $item->nameToView();
                unset($item->guard_name, $item->team_id, $item->created_at, $item->updated_at);
            })->collect()
        ];

        return Responder::send_success("", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $customer_id = $customer->id;
        if (!$customer->activeSubscription->hasUserCount()) {
            return Responder::send_unprocessable("Kullanıcı limitiniz dolmuştur. Lütfen aboneliğinizi yükseltiniz.");
        }
        $msg = "Kayıt başarısız!";
        $errors = [];
        $input = $request->all();
        //validator
        $validator = Validator::make($input, [
            "fullname" => "required",
            "email" => "required|email|unique:user,email",
            "password" => "required|min:6",
            "phone" => "required|max:20",
            "repass" => "required|same:password",
            "role_id" => [
                "required",
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('team_id', SanctumHelper::customer_id())->where('name', '!=', PermissionRoleEnum::ADMIN->value);
                }),
                'distinct',
            ]
        ], [
            "fullname.required" => "Ad Soyad alanı boş bırakılamaz!",
            "email.required" => "Email alanı boş bırakılamaz!",
            "email.email" => "Geçerli bir email adresi giriniz!",
            "email.unique" => "Bu email adresi kullanılmaktadır!",
            "password.required" => "Şifre alanı boş bırakılamaz!",
            "password.min" => "Şifre en az 6 karakter olmalıdır!",
            "phone.required" => "Telefon alanı boş bırakılamaz!",
            "phone.max" => "Telefon alanı en fazla 20 karakter olmalıdır!",
            "repass.required" => "Şifre tekrarı alanı boş bırakılamaz!",
            "repass.same" => "Şifreler uyuşmuyor!",
            "role_id.required" => "Rol alanı boş bırakılamaz!",
            "role_id.exists" => "Geçersiz rol!",
            "role_id.distinct" => "Geçersiz rol!"
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable($msg, $errors);
        } else {
            $input['password'] = bcrypt(trim($input['password']));

            $user = User::create([
                'customer_id' => SanctumHelper::customer_id(),
                'fullname' => trim($input['fullname']),
                'email' => trim($input['email']),
                'password' => trim($input['password']),
                'role_id' => trim($input['role_id']),
                'phone' => trim($input['phone']),
            ]);
            $role_id = Role::where('id', $input['role_id'])->first();
            $user->assignRole($role_id);
            $user->markEmailAsVerified();

            $msg = "Kayıt başarılı!";
            $customer->activeSubscription?->decreaseUserCount(1);

            return Responder::send_success($msg);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $user = User::where('customer_id', SanctumHelper::customer_id())->findOrFail($id);
        return Responder::send_success("", $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $user = User::findOrFail($id);

        if ($user) {
            $input = $request->all();
            $validator = Validator::make($input, [
                "fullname" => "required",
                "role_id" => [
                    "required",
                    Rule::exists('roles', 'id')->where(function ($query) {
                        $query->where('team_id', SanctumHelper::customer_id())->where('name', '!=', PermissionRoleEnum::ADMIN->value);
                    }),
                    'distinct',
                ]
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return Responder::send_unprocessable("", $errors);
            } else {
                $user->role_id = $input['role_id'];
                $user->fullname = $input['fullname'];
                $user->phone = $input['phone'];
                $user->save();
                $role_id = Role::where('id', $input['role_id'])->first();
                $user->assignRole($role_id);
                $user->tokens()->delete();
                return Responder::send_success("Update Success!");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(string $id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $customer_id = $customer->id;
        $user = User::where('customer_id', $customer_id)->findOrFail($id);
        if ($user) {
            $customer->activeSubscription?->increaseUserCount(1);
            $user->delete();
            return Responder::send_success("Delete Success!");
        }
    }
}
