<?php

namespace App\Libraries\RouteGenerator\Api\Routes\ClientRoutes;

use App\Http\Controllers\Client\Customer\CustomerOfferController;
use Illuminate\Support\Facades\Route;

class RouteCompanyCustomerOffers
{

    public static function generate()
    {
        // /offers
        Route::get('/offers', [CustomerOfferController::class, 'index'])->name('company_customer.offers');
        // /offers/{customer_offer}
        Route::get('/offers/{customer_offer}', [CustomerOfferController::class, 'show'])->name('company_customer.offers.show');
        //edit
        Route::get('/offers/{customer_offer}/edit', [CustomerOfferController::class, 'edit'])->name('company_customer.offers.edit');

        //excel export
        Route::get('/offers/{customer_offer}/export-excel', [CustomerOfferController::class, 'exportToExcel'])->name('company_customer.offers.export');

        Route::prefix('{company_customer}/offers')->group(function () {
            //index
            Route::get('/', [CustomerOfferController::class, 'list'])->name('company_customer.offers.list');
            //store
            Route::post('/', [CustomerOfferController::class, 'store'])->name('company_customer.offers.store');
            //update
            Route::put('/{customer_offer}', [CustomerOfferController::class, 'update'])->name('company_customer.offers.update');

            //update status
            Route::patch('/{customer_offer}/status', [CustomerOfferController::class, 'statusUpdate'])->name('company_customer.offers.update.status');
            //destroy
            Route::delete('/{customer_offer}', [CustomerOfferController::class, 'destroy'])->name('company_customer.offers.destroy');
            //convert to order
            Route::post('/{customer_offer}/convert-to-order', [CustomerOfferController::class, 'convertToOrder'])->name('company_customer.offers.convert-to-order');
        });
    }
}
