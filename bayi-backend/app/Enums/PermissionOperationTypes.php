<?php

namespace App\Enums;

enum PermissionOperationTypes: string
{
    case CREATE = 'create';
    case VIEW = 'view';
    case UPDATE = 'update';
    case DELETE = 'delete';

    public static function allValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function generatePermission(PermissionTypes $permissionType): string
    {
        return $this->value . '-' . $permissionType->value;
    }
}
