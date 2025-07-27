<?php

namespace App\Libraries\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomerUser;
use App\Models\System\Role;

class SanctumDealerHelper
{
    const GUARD = 'sanctum';

    /** @return CompanyCustomerUser|null */
    private static function __getUser()
    {
        return auth(self::GUARD)->user();
    }

    public static function getUser()
    {
        return self::__getUser();
    }

    public static function user_id()
    {
        return self::__getUser()->id;
    }

    public static function customer_id()
    {
        return self::__getUser()->customer_id;
    }

    public static function company_customer_id()
    {
        return self::__getUser()->companyCustomer->id;
    }

    public static function companyCustomer()
    {
        return self::__getUser()->companyCustomer;
    }

    public static function getCustomer()
    {
        return self::__getUser()->customer;
    }
}
