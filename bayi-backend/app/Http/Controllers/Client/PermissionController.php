<?php

namespace App\Http\Controllers\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionRoleEnum;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PermissionRequest;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Permissions\RolePermissionToList;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\System\Permission;
use App\Models\System\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    private $permissionClass = PermissionTypes::permission;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $data = Role::where('team_id', '=', getPermissionsTeamId())
            ->where('name', '!=', PermissionRoleEnum::ADMIN->value)
            ->get()->each(function ($item) {
                /** @var Role $item */
                $item->name = $item->nameToView();
                $item->count = $item->users()->where('team_id', '=', getPermissionsTeamId())->count();
                unset($item->guard_name, $item->team_id, $item->created_at, $item->updated_at);
            })->collect();
        return DatatableResponder::sendResponse($data, $data->count());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $permission = Role::where('team_id', '=', $customer_id)
            ->where('name', '!=', PermissionRoleEnum::ADMIN->value)
            ->where('id', '=', $id)->firstOrFail();

        $listModel = new RolePermissionToList($customer_id, $permission->id);
        $data = $listModel->generateListForClient();

        return Responder::send_success("", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $validated = $request->validated();
        $inputPermissions = $validated['data']['items'];
        $activePermissionsIdList = [];
        foreach ($inputPermissions as $key => $value) {
            foreach ($value['permissions'] as $key2 => $value2) {
                if ($value2['checked']) {
                    $activePermissionsIdList[] = Permission::where('id', '=', $value2['id'])->get()->first();
                }
            }
        }
        $role = Role::where('id', '=', $id)->get()->first();

        $role->syncPermissions($activePermissionsIdList);

        //delete token of user
        $users = User::where('role_id', '=', $id)->get();
        foreach ($users as $user) {
            $user->tokens()->delete();
        }



        return Responder::send_success("");
    }
}
