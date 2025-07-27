<?php

namespace App\Libraries\RouteGenerator\Api\Base;

class RouteGeneratorBase
{
    const SPFX_AUTH = 'auth:sanctum';
    const SPFX_ADMIN = 'abilities:admin';
    const SPFX_SERVICE = 'abilities:user';
    const SPFX_USERSTATUS = 'userstatus';

    //for company customer user
    const SPFX_COMPANY_CUSTOMER_USER = 'abilities:company_customer_user';
}
