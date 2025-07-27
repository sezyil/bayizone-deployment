<?php

namespace App\Libraries\Permissions\Core;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionRoleEnum;
use App\Enums\PermissionTypes;

class PermissionCore
{
    public $roles = [];

    public $operation_types = [];

    public $permission_types = [];

    public function __construct()
    {
        $this->roles = PermissionRoleEnum::allValues();
        $this->operation_types = PermissionOperationTypes::allValues();
        $this->permission_types = PermissionTypes::allValues();
    }
}
