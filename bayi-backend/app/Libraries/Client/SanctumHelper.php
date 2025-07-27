<?php

namespace App\Libraries\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Libraries\Response\Responder;
use App\Models\Customer;
use App\Models\System\Role;
use App\Models\User;

class SanctumHelper
{
    const GUARD = 'sanctum';
    /**
     * User
     * @return User|null
     */
    public static function getUser()
    {
        return auth(self::GUARD)->user();
    }

    public static function userLanguage()
    {
        return self::getUser()->language;
    }

    public static function user_id()
    {
        return self::getUser()->id;
    }

    public static function customer_id()
    {
        return self::getUser()->customer_id;
    }

    /**
     * Get Customer
     *
     * @return Customer|null
     */
    public static function getCustomer()
    {
        return self::getUser()->customer;
    }
}
