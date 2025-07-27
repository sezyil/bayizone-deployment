<?php

namespace App\Libraries\Permissions;

use App\Enums\PermissionRoleEnum;
use App\Libraries\Permissions\Core\PermissionCore;
use App\Models\System\Permission;
use App\Models\System\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionGenerator extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate default permissions and create them if not exists
     *
     * @return void
     */
    public static function generateDefaultPermissionsClient($rebuild = false)
    {
        $instance = new self();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if ($rebuild) {
            // delete all permissions
            Permission::query()->delete();
            //remove non used roles
            $roles = Role::all();
            $existRoles = PermissionRoleEnum::allValues();
            foreach ($roles as $role) {
                if (!in_array($role->name, $existRoles)) {
                    $role->delete();
                }
            }
        }

        // create permissions
        foreach ($instance->operation_types as $type) {
            foreach ($instance->permission_types as $permission) {
                if ($permission == 'customer' && ($type == 'create' || $type == 'delete')) {
                    continue;
                }
                if ($permission == 'offer_request' && ($type == 'create' || $type == 'delete' || $type == 'update')) {
                    continue;
                }
                $name =  $type . '-' . $permission;
                if (!Permission::where('name', $name)->first())
                    Permission::create([
                        'id' => \Str::uuid()->toString(),
                        'name' => $name, 'guard_name' => 'user'
                    ]);
            }
        }
    }

    public static function generateForNewRegister(string $customer_id)
    {
        $instance = new self();
        foreach ($instance->roles as $role) {
            $created = Role::create([
                'id' => \Str::uuid()->toString(), // role id
                'name' => $role,
                'guard_name' => 'user',
                'team_id' => $customer_id
            ]);
        }
        $role = Role::where('name', '=', 'administrator')->where('team_id', '=', $customer_id)->first();
        $role->givePermissionTo(Permission::all());
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        return $role;
    }

    //after change permission type or operation type
    public static function rebuildForSystemUpdate()
    {
        self::generateDefaultPermissionsClient(true);
        //reassign permissions
        $customers = \App\Models\Customer::all();
        foreach ($customers as $customer) {
            setPermissionsTeamId($customer->id);
            $role = Role::findByName('administrator', 'user');
            $role->syncPermissions(Permission::all());
        }
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
