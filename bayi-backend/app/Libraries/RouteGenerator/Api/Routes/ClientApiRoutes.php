<?php

namespace App\Libraries\RouteGenerator\Api\Routes;

use App\Http\Controllers\Client\Attributes\AttributeGroupController;
use App\Http\Controllers\Client\Attributes\AttributesController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\BatchProcessController;
use App\Http\Controllers\Client\Category\CategoryController;
use App\Http\Controllers\Client\CompanyCustomer\CompanyCustomerController;
use App\Http\Controllers\Client\Customer\CustomerBankAccountController;
use App\Http\Controllers\Client\Customer\CustomerController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\OfferRequestController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\PermissionController;
use App\Http\Controllers\Client\PlanController;
use App\Http\Controllers\Client\Product\ProductController;
use App\Http\Controllers\Client\Product\ProductImagesController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ShipmentController;
use App\Http\Controllers\Client\TransactionController;
use App\Http\Controllers\Client\TransactionDetailController;
use App\Http\Controllers\Client\UnitController;
use App\Http\Controllers\Client\UsersController;
use App\Http\Controllers\Client\VariantController;
use App\Http\Controllers\Client\VariantValueController;
use App\Libraries\RouteGenerator\Api\Base\RouteGeneratorBase;
use App\Libraries\RouteGenerator\Api\Routes\ClientRoutes\RouteCompanyCustomerBankAccount;
use App\Libraries\RouteGenerator\Api\Routes\ClientRoutes\RouteCompanyCustomerNotes;
use App\Libraries\RouteGenerator\Api\Routes\ClientRoutes\RouteCompanyCustomerOffers;
use App\Libraries\RouteGenerator\Api\Routes\ClientRoutes\RouteCompanyCustomerOrders;
use App\Libraries\RouteGenerator\Api\Routes\ClientRoutes\RouteCompanyCustomerWarehouses;
use Illuminate\Support\Facades\Route;

class ClientApiRoutes extends RouteGeneratorBase
{

    public static function generate()
    {
        //Customer Routes
        Route::prefix('client')->middleware([self::SPFX_SERVICE, self::SPFX_USERSTATUS])->group(function () {
            Route::prefix('auth')->controller(AuthController::class)->group(function () {
                //non-login required
                Route::withoutMiddleware([self::SPFX_SERVICE, self::SPFX_AUTH, self::SPFX_USERSTATUS])->group(function () {
                    Route::post('login', 'login');
                    Route::post('register', 'register');
                    Route::post('forgot-password', 'forgotPassword');
                    Route::post('reset-password', 'resetPassword');
                });

                Route::get('getInfo', 'userData')->name('userData');
                Route::post('logout', 'logout')->name('logout');
            });

            //category autocomplete
            Route::get('categories/autocomplete', [CategoryController::class, 'autocomplete'])->name('categories.autocomplete');
            //attribute group autocomplete
            Route::get('attribute_groups/autocomplete', [AttributeGroupController::class, 'autocomplete'])->name('attribute_groups.autocomplete');
            //attribute autocomplete
            Route::get('attributes/autocomplete', [AttributesController::class, 'autocomplete'])->name('attributes.autocomplete');

            //variant
            Route::prefix('variants')->as('variants.')->group(function () {
                Route::get('/', [VariantController::class, 'index'])->name('index');
                Route::post('/', [VariantController::class, 'store'])->name('store');
                Route::get('/{id}', [VariantController::class, 'show'])->name('show');
                Route::put('/{id}', [VariantController::class, 'update'])->name('update');
                Route::delete('/{id}', [VariantController::class, 'destroy'])->name('destroy');
            });

            //variant values
            Route::prefix('variant_values')->as('variant_values.')->group(function () {
                Route::get('/', [VariantValueController::class, 'index'])->name('index');
                Route::post('/', [VariantValueController::class, 'store'])->name('store');
                Route::get('/{id}', [VariantValueController::class, 'show'])->name('show');
                Route::put('/{id}', [VariantValueController::class, 'update'])->name('update');
                Route::delete('/{id}', [VariantValueController::class, 'destroy'])->name('destroy');
            });

            Route::get('products/autocomplete', [ProductController::class, 'autocomplete'])->name('product.autocomplete');
            Route::post('products/batch_upload', [ProductController::class, 'batchUpload'])->name('product.batch');
            Route::post('products/duplicate/{id}', [ProductController::class, 'duplicate'])->name('product.duplicate');
            Route::post('products/sync_ai/{id}', [ProductController::class, 'syncAi'])->name('product.sync_ai');


            Route::resources([
                'users' => UsersController::class,
                'attribute_groups' => AttributeGroupController::class,
                'attributes' => AttributesController::class,
                'categories' => CategoryController::class,
                'products' => ProductController::class,
                'product_image' => ProductImagesController::class,
                'bank_accounts' => CustomerBankAccountController::class,
                'product_units' => UnitController::class,
                'permissions' => PermissionController::class,
                'offer_requests' => OfferRequestController::class,
                'batch_processes' => BatchProcessController::class,
            ]);

            Route::prefix('transactions')->as('transactions.')->group(function () {
                //default
                Route::get('/', [TransactionController::class, 'index'])->name('index');
                Route::post('/', [TransactionController::class, 'store'])->name('store');
                Route::get('/create', [TransactionController::class, 'create'])->name('create');
                //export excel
                Route::get('/export-excel', [TransactionController::class, 'exportToExcel'])->name('export-excel');
                //balance
                Route::get('/balance', [TransactionController::class, 'balance'])->name('balance');
                Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
                Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
                Route::get('/{transaction}/payment', [TransactionController::class, 'markAsPaid'])->name('payment');
            });




            //dashboard
            Route::prefix('dashboard')->group(function () {
                Route::get('cards', [DashboardController::class, 'cardData'])->name('dashboard.cardData');
                Route::get('order_summary', [DashboardController::class, 'orderSummary'])->name('dashboard.orderSummary');
                Route::get('offer_summary', [DashboardController::class, 'offerSummary'])->name('dashboard.offerSummary');
                Route::get('last_offers_orders', [DashboardController::class, 'lastFiveOrdersOffers'])->name('dashboard.productSummary');
                Route::get('last_transactions', [DashboardController::class, 'lastFiveTransactions'])->name('dashboard.lastTransactions');
                Route::get('pie_chart', [DashboardController::class, 'pieChart'])->name('dashboard.pieChart');
            });

            //prefix profile
            Route::prefix('profile')->group(function () {
                //for company
                Route::prefix('company')->group(function () {
                    Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
                    Route::put('/', [CustomerController::class, 'update'])->name('customer.update');
                    Route::post('image', [CustomerController::class, 'imageUpdate'])->name('customer.imageUpdate');
                    Route::get('services', [CustomerController::class, 'services'])->name('customer.services');
                    //update services
                    Route::post('services', [CustomerController::class, 'updateServices'])->name('customer.updateServices');
                });

                //for profile
                Route::get('user', [ProfileController::class, 'index'])->name('profile.index');
                Route::put('user', [ProfileController::class, 'update'])->name('profile.update');
            });

            //company customer prefix
            Route::prefix('company_customers')->group(function () {
                //index
                Route::get('/', [CompanyCustomerController::class, 'index'])->name('company_customer.index');
                //store
                Route::post('/', [CompanyCustomerController::class, 'store'])->name('company_customer.store');
                //edit
                Route::get('/{company_customer}/edit', [CompanyCustomerController::class, 'edit'])->name('company_customer.edit');
                //update
                Route::put('/{company_customer}', [CompanyCustomerController::class, 'update'])->name('company_customer.update');
                //destroy
                Route::delete('/{company_customer}', [CompanyCustomerController::class, 'destroy'])->name('company_customer.destroy');

                //create createCustomerUser
                Route::post('/{company_customer}/createCustomerUser', [CompanyCustomerController::class, 'createCustomerUser'])->name('company_customer.createCustomerUser');

                //company customer bank account prefix
                RouteCompanyCustomerBankAccount::generate();

                //company customer offer prefix
                RouteCompanyCustomerOffers::generate();

                //company customer warehouse prefix
                RouteCompanyCustomerWarehouses::generate();

                RouteCompanyCustomerOrders::generate();

                RouteCompanyCustomerNotes::generate();
            });

            //plans
            Route::prefix('plans')->group(function () {
                Route::get('/', [PlanController::class, 'index'])->name('plans.index');
                Route::get('/{id}', [PlanController::class, 'show'])->name('plans.show');
                Route::post('/{id}/purchase', [PlanController::class, 'purchase'])->name('plans.purchase');
            });

            //payment
            Route::prefix('payments')->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
                Route::get('/available_bank_transfer', [PaymentController::class, 'availableBankTransfer'])->name('payments.availableBankTransfer');
                Route::get('/{id}', [PaymentController::class, 'show'])->name('payments.show');
                Route::get('/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
                Route::post('{id}/purchase', [PaymentController::class, 'purchase'])->name('payments.purchase');
                Route::patch('{id}/payment_method', [PaymentController::class, 'updatePaymentMethod'])->name('payments.paymentMethod');
                Route::patch('{id}/payment_notification', [PaymentController::class, 'paymentNotification'])->name('payments.paymentNotification');
                Route::patch('{id}/coupon', [PaymentController::class, 'applyCoupon'])->name('payments.applyCoupon');
            });

            //shipments
            Route::prefix('shipments')->as('shipments.')->group(function () {
                Route::get('/', [ShipmentController::class, 'index'])->name('index');
                Route::post('/available_orders', [ShipmentController::class, 'availableOrders'])->name('availableOrders');
                Route::post('/', [ShipmentController::class, 'store'])->name('store');
                Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('show');
                Route::get('/{shipment}/edit', [ShipmentController::class, 'edit'])->name('edit');
                Route::put('/{shipment}', [ShipmentController::class, 'update'])->name('update');
                Route::delete('/{shipment}', [ShipmentController::class, 'destroy'])->name('destroy');
                //export excel
                Route::get('/{shipment}/export-excel', [ShipmentController::class, 'exportToExcel'])->name('export-excel');

                //approve
                Route::put('/{shipment}/approve', [ShipmentController::class, 'approve'])->name('approve');
                //send
                Route::put('/{shipment}/send', [ShipmentController::class, 'sendShipment'])->name('send');
                //deliver
                Route::put('/{shipment}/deliver', [ShipmentController::class, 'deliverShipment'])->name('deliver');
            });
        });
    }
}
