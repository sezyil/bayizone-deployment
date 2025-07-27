<?php

namespace App\Libraries\RouteGenerator\Api\Routes;

use App\Http\Controllers\Admin\AuthorizationController;
use App\Libraries\RouteGenerator\Api\Base\RouteGeneratorBase;
use Illuminate\Support\Facades\Route;

class AdminApiRoutes extends RouteGeneratorBase
{
    public static function generate()
    {
        //Admin Routes
        Route::prefix('admin')->middleware(self::SPFX_ADMIN)->group(function () {
            //Admin Auth Routes
            Route::prefix('auth')->controller(AuthorizationController::class)->group(function () {
                //non-login required
                Route::withoutMiddleware([self::SPFX_ADMIN, self::SPFX_AUTH])->group(function () {
                    Route::post('login', 'login');
                    Route::post('register', 'register');
                });

                Route::get('getInfo', 'userData')->name('userData');
            });
        })->name('admin.');
    }
}
