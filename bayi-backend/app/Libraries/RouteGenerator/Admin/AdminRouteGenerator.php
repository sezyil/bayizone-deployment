<?php

namespace App\Libraries\RouteGenerator\Admin;


use Illuminate\Support\Facades\Route;

/**
 * Admin Route Generator for web.php
 */
class AdminRouteGenerator
{


    public static function  generate()
    {
        $generateRoutes = function () {
            //auth
            Route::prefix('auth')->as('auth.')->group(function () {
                Route::get('login', [\App\Http\Controllers\Admin\AuthorizationController::class, 'showLoginForm'])->name('login')->withoutMiddleware('auth:admin');
                Route::post('login', [\App\Http\Controllers\Admin\AuthorizationController::class, 'login'])->name('login.post')->withoutMiddleware('auth:admin');
                Route::get('logout', [\App\Http\Controllers\Admin\AuthorizationController::class, 'logout'])->name('logout');
            });
            Route::prefix('customer')->as('customer.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('index');
                //Route::get('create', [\App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('create');
                //Route::post('store', [\App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('store');
                //Route::get('edit/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('edit');
                //Route::post('update/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('update');
                //Route::get('delete/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'delete'])->name('delete');
            });
            //order
            Route::prefix('order')->as('order.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
                //Route::get('create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('create');
                //Route::post('store', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('store');
                Route::get('{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
                //Route::get('edit/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('edit');
                //Route::post('update/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('update');
                //Route::get('delete/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'delete'])->name('delete');
                Route::get('{id}/approve', [\App\Http\Controllers\Admin\OrderController::class, 'approve'])->name('approve');
            });

            //coupon
            Route::prefix('coupon')->as('coupon.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('index');
                Route::get('create', [\App\Http\Controllers\Admin\CouponController::class, 'create'])->name('create');
                Route::post('store', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('store');
                Route::get('{id}', [\App\Http\Controllers\Admin\CouponController::class, 'show'])->name('show');
                Route::get('{id}/edit', [\App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('edit');
                Route::post('{id}/update', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('update');
                Route::get('{id}/delete', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('delete');
            });

            Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');
        };
        //admin

        if (env('APP_ENV') == 'local') {
            Route::prefix('admin')
                ->middleware(['web', 'auth:admin'])
                ->as('admin.')->group($generateRoutes);
        } else {
            Route::domain(config('app.admin_domain'))
                ->middleware(['web', 'auth:admin'])
                ->as('admin.')->group($generateRoutes);
        }
    }
}
