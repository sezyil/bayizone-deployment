<?php

namespace App\Libraries\RouteGenerator\Api\Routes\ClientRoutes;

use App\Http\Controllers\Client\Customer\CustomerOrderController;
use Illuminate\Support\Facades\Route;

class RouteCompanyCustomerOrders
{

    public static function generate()
    {
        // /orders
        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('company_customer_order.orders');
        // /orders/{customer_offer}
        Route::get('/orders/{customer_offer}', [CustomerOrderController::class, 'show'])->name('company_customer_order.orders.show');
        //edit
        Route::get('/orders/{customer_offer}/edit', [CustomerOrderController::class, 'edit'])->name('company_customer_order.orders.edit');
        Route::get('/orders/{customer_offer}/export-excel', [CustomerOrderController::class, 'exportToExcel'])->name('company_customer.orders.export');
        //lineSentQuantityUpdate
        Route::patch('/orders/{customer_offer}/lines/{customer_order_line}/history', [CustomerOrderController::class, 'updateLineHistory'])
            ->name('company_customer_order.orders.lineSentQuantityUpdate');
        //deleteLineHistory
        Route::delete('/orders/{customer_offer}/lines/{customer_order_line}/history/{customer_order_line_history}', [CustomerOrderController::class, 'deleteLineHistory'])
            ->name('company_customer_order.orders.deleteLineHistory');
        //update status
        Route::patch('/orders/{customer_offer}/status', [CustomerOrderController::class, 'statusUpdate'])->name('company_customer_order.orders.update.status');


        Route::prefix('{company_customer}/orders')->group(function () {
            //index
            Route::get('/', [CustomerOrderController::class, 'list'])->name('company_customer_order.orders.list');
            //store
            Route::post('/', [CustomerOrderController::class, 'store'])->name('company_customer_order.orders.store');
            //update
            Route::put('/{customer_offer}', [CustomerOrderController::class, 'update'])->name('company_customer_order.orders.update');

            //history
            Route::get('/{customer_offer}/history', [CustomerOrderController::class, 'history'])->name('company_customer_order.orders.history');

            //destroy
            Route::delete('/{customer_offer}', [CustomerOrderController::class, 'destroy'])->name('company_customer_order.orders.destroy');
        });
    }
}
