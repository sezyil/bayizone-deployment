<?php

namespace App\Libraries\RouteGenerator\Api\Routes\ClientRoutes;

use App\Http\Controllers\Client\CompanyCustomer\CompanyCustomerWarehouseController;
use Illuminate\Support\Facades\Route;

class RouteCompanyCustomerWarehouses
{

    public static function generate()
    {
        //warehouses
        Route::get('/warehouses', [CompanyCustomerWarehouseController::class, 'index'])->name('company_customer.warehouses');
        Route::prefix('{company_customer}/warehouses')->group(function () {
            //index
            Route::get('/', [CompanyCustomerWarehouseController::class, 'list'])->name('company_customer.warehouses.list');
            //store
            Route::post('/', [CompanyCustomerWarehouseController::class, 'store'])->name('company_customer.warehouses.store');
            //edit
            Route::get('/{warehouse}/edit', [CompanyCustomerWarehouseController::class, 'edit'])->name('company_customer.warehouses.edit');
            //update
            Route::put('/{warehouse}', [CompanyCustomerWarehouseController::class, 'update'])->name('company_customer.warehouses.update');
            //destroy
            Route::delete('/{warehouse}', [CompanyCustomerWarehouseController::class, 'destroy'])->name('company_customer.warehouses.destroy');
        });
    }
}
