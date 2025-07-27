<?php

namespace App\Libraries\RouteGenerator\Api\Routes\ClientRoutes;

use App\Http\Controllers\Client\CompanyCustomer\CompanyCustomerNotesController;
use Illuminate\Support\Facades\Route;

class RouteCompanyCustomerNotes
{

    public static function generate()
    {
        // /offers
        Route::get('/notes', [CompanyCustomerNotesController::class, 'index'])->name('company_customer_note');
        // /offers/{customer_offer}
        Route::prefix('{company_customer}/notes')->group(function () {
            //index
            Route::get('/', [CompanyCustomerNotesController::class, 'list'])->name('company_customer_note.list');
            //edit
            Route::get('/{note}/edit', [CompanyCustomerNotesController::class, 'edit'])->name('company_customer_note.edit');
            //store
            Route::post('/', [CompanyCustomerNotesController::class, 'store'])->name('company_customer_note.store');
            //update
            Route::put('/{note}', [CompanyCustomerNotesController::class, 'update'])->name('company_customer_note.update');
            //destroy
            Route::delete('/{note}', [CompanyCustomerNotesController::class, 'destroy'])->name('company_customer_note.destroy');
        });
    }
}
