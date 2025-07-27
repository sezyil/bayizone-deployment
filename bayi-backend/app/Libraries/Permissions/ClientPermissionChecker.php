<?php

namespace App\Libraries\Permissions;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\Responder;
use App\Models\System\Role;
use App\Models\User;

class ClientPermissionChecker
{
    /**
     * @param PermissionOperationTypes $type
     * @param PermissionTypes $permissionClass
     * @param array $externalPermissions - external permissions to check if the user has any of them. array item format:
     * 'permissionOperationType-permissionType'
     */
    public static function check(PermissionOperationTypes $type, PermissionTypes $permissionClass, $externalPermissions = [])
    {
        /** @var User $user */
        $user = SanctumHelper::getUser();
        $role = Role::where('id', $user->role_id)->where('team_id', $user->customer_id)->first();
        $permission = $type->value . '-' . $permissionClass->value;
        if (!$role->hasPermissionTo($permission)) {
            $has = false;
            foreach ($externalPermissions as $externalPermission) {
                $permission = explode('-', $externalPermission);
                if ($role->hasPermissionTo($permission[0] . '-' . $permission[1])) {
                    $has = true;
                    break;
                }
            }
            if (!$has) {
                Responder::send_forbidden()->throwResponse();
                die();
            }
        }
    }
}
