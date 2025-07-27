<?php

namespace App\Libraries\RouteGenerator\Api;

use App\Http\Controllers\PaymentCallbackController;
use App\Libraries\RouteGenerator\Api\Base\RouteGeneratorBase;
use App\Libraries\RouteGenerator\Api\Routes\ClientApiRoutes;
use App\Libraries\RouteGenerator\Api\Routes\DealerApiRoutes;
use App\Libraries\RouteGenerator\Api\Routes\PublicApiRoutes;
use App\Libraries\RouteGenerator\Api\Routes\StoreApiRoutes;
use Illuminate\Support\Facades\Route;

class ApiRouteGenerator extends RouteGeneratorBase
{


    public static function generate()
    {
        Route::middleware(self::SPFX_AUTH)->group(
            function () {
                //AdminApiRoutes::generate();
                ClientApiRoutes::generate();
                DealerApiRoutes::generate();
            }
        );
        StoreApiRoutes::generate();

        PublicApiRoutes::generate();

        //payment/callback
        Route::post('payment/callback', [PaymentCallbackController::class, 'iyzicoCallback'])->name('payment.callback');
    }
}
