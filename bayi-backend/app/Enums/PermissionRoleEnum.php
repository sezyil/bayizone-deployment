<?php
namespace App\Enums;

enum PermissionRoleEnum: string
{
    case ADMIN = "administrator";
    case USER = "user";

    public static function allValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
