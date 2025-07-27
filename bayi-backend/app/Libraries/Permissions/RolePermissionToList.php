<?php

namespace App\Libraries\Permissions;

use App\Enums\PermissionTypes;
use App\Libraries\Permissions\Core\PermissionCore;
use App\Models\System\Permission;
use App\Models\System\Role;

class RolePermissionToList extends PermissionCore
{
    private string $customer_id;
    private string $role_id;
    public function __construct(string $customer_id, $role_id)
    {
        parent::__construct();
        $this->customer_id = $customer_id;
        $this->role_id = $role_id;
    }

    public function generateListForClient(): array
    {
        $permissions = Permission::all();
        $role = Role::find($this->role_id);
        $tmp = [
            'name' => $role->nameToView(),
            'id' => $this->role_id,
            'items' => []
        ];
        $rolePermissions = $role->permissions()->get();
        $rolePermissions = $rolePermissions->map(function ($item) {
            return $item->name;
        })->toArray();
        $permissions = $permissions->map(function ($item) use ($rolePermissions, &$tmp) {
            $checked = in_array($item->name, $rolePermissions);
            $split = explode('-', $item->name);
            $operation = $split[0];
            $type = $split[1];
            if (!isset($tmp['items'][$type])) {
                $tmp['items'][$type] = [
                    'name' => $this->typeTranslator($type),
                    'permissions' => []
                ];
            }

            $tmp['items'][$type]['permissions'][$operation] = [
                'id' => $item->id,
                'operation_label' => $this->operationTranslator($operation),
                'operation' => $operation,
                'checked' => $checked
            ];

            return $item;
        })->toArray();

        //sort by name
        usort($tmp['items'], function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $tmp;
    }

    //operation Translator
    private function operationTranslator(string $operation): string
    {
        $label = '';
        switch ($operation) {
            case 'create':
                $label = 'Oluşturma';
                break;
            case 'view':
                $label = 'Görüntüleme';
                break;
            case 'update':
                $label = 'Güncelleme';
                break;
            case 'delete':
                $label = 'Silme';
                break;
            default:
                $label = 'Tanımsız';
                break;
        }
        return $label;
    }

    //type Translator
    private function typeTranslator($type): string
    {
        return PermissionTypes::translate($type);
    }
}
