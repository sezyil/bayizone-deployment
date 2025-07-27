<?php

namespace App\Libraries\RouteGenerator\Api\Routes;


use App\Http\Controllers\Dealer\AuthController;
use App\Http\Controllers\Dealer\BankAccountController;
use App\Http\Controllers\Dealer\DashboardController;
use App\Http\Controllers\Dealer\OfferController;
use App\Http\Controllers\Dealer\OfferRequest\OfferRequestController;
use App\Http\Controllers\Dealer\OfferRequest\OfferRequestProductController;
use App\Http\Controllers\Dealer\OrderController;
use App\Http\Controllers\Dealer\ProfileController;
use App\Http\Controllers\Dealer\ShipmentController;
use App\Http\Controllers\Dealer\UsersController;
use App\Http\Controllers\Dealer\WarehouseController;
use App\Libraries\RouteGenerator\Api\Base\RouteGeneratorBase;
use Illuminate\Support\Facades\Route;

class DealerApiRoutes extends RouteGeneratorBase
{

    public static function generate()
    {
        //Customer Routes
        Route::prefix('dealer')->name('dealer')->middleware([self::SPFX_COMPANY_CUSTOMER_USER])->group(function () {
            Route::prefix('auth')->controller(AuthController::class)->group(function () {
                //non-login required
                Route::withoutMiddleware([self::SPFX_COMPANY_CUSTOMER_USER, self::SPFX_AUTH])->group(function () {
                    Route::post('login', 'login')->name('login');
                    //Route::post('register', 'register')->name('register');
                    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
                    Route::post('reset-password', 'resetPassword')->name('reset-password');
                });

                Route::get('getInfo', 'userData')->name('userData');
                Route::post('logout', 'logout')->name('logout');
            });

            Route::resources([
                //users
                'users' => UsersController::class,
                'warehouses' => WarehouseController::class,
                'bank_accounts' => BankAccountController::class,
                'offer_request_products' => OfferRequestProductController::class,
                'offer_requests' => OfferRequestController::class,
            ]);

            Route::get('orders/{order}/export-excel', [OrderController::class, 'exportToExcel'])->name('orders.export');
            Route::resource('orders', OrderController::class)->only(['index', 'show']);

            //dashboard
            Route::get('dashboard', DashboardController::class . '@index')->name('dashboard.index');

            //prefix profile
            Route::prefix('profile')->group(function () {
                //for profile
                Route::get('user', [ProfileController::class, 'index'])->name('profile.index');
                Route::put('user', [ProfileController::class, 'update'])->name('profile.update');
                //language
                Route::put('user/language', [ProfileController::class, 'language'])->name('profile.language');
            });

            Route::get('offers', OfferController::class . '@index')->name('offers.index');

            Route::prefix('shipments')->as('shipments.')->group(function () {
                Route::get('/', [ShipmentController::class, 'index'])->name('index');
                Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('show');
                Route::get('/{shipment}/export-excel', [ShipmentController::class, 'exportToExcel'])->name('export-excel');
            });
        });
    }
}
