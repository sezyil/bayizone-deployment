<?php

namespace App\Libraries\RouteGenerator\Api\Routes;

use App\Http\Controllers\UtilitiesController;
use App\Libraries\RouteGenerator\Api\Base\RouteGeneratorBase;
use Illuminate\Support\Facades\Route;

class PublicApiRoutes extends RouteGeneratorBase
{
    public static function generate()
    {
        //Utilities
        Route::prefix('utilities')->group(function () {
            Route::controller(UtilitiesController::class)->group(function () {
                Route::get('countries', 'Countries');
                Route::get('country/{id}', 'States')->where('id', '[0-9]+');
                Route::get('states/{id}', 'Cities')->where('id', '[0-9]+');
                Route::get('currencies', 'Currencies');
                Route::get('product_batch_sample/csv', 'batchCsvSample');
                Route::get('product_batch_sample/json', 'batchJsonSample');
                Route::get('client-privacy-policy', 'clientPrivacyPolicy');
            });
        });

        // unauthorized
        Route::get('unauthorized', function () {
            return response()->json(['error' => 'Unauthorized.'], 401);
        })->name('unauthorized');
    }
}
