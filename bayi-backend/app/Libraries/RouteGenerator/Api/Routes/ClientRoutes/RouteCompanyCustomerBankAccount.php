<?php

namespace App\Libraries\RouteGenerator\Api\Routes\ClientRoutes;

use App\Http\Controllers\Client\CompanyCustomer\CompanyCustomerBankAccountController;
use Illuminate\Support\Facades\Route;

class RouteCompanyCustomerBankAccount
{

    public static function generate()
    {
        Route::get('/bank_accounts', [CompanyCustomerBankAccountController::class, 'index'])->name('company_customer.bank_account.index');
        Route::prefix('{company_customer}/bank_accounts')->group(function () {
            //index
            Route::get('/', [CompanyCustomerBankAccountController::class, 'list'])->name('company_customer.bank_account.list');
            //store
            Route::post('/', [CompanyCustomerBankAccountController::class, 'store'])->name('company_customer.bank_account.store');
            //edit
            Route::get('/{company_customer_bank_account}/edit', [CompanyCustomerBankAccountController::class, 'edit'])->name('company_customer.bank_account.edit');
            //update
            Route::put('/{company_customer_bank_account}', [CompanyCustomerBankAccountController::class, 'update'])->name('company_customer.bank_account.update');
            //destroy
            Route::delete('/{company_customer_bank_account}', [CompanyCustomerBankAccountController::class, 'destroy'])->name('company_customer.bank_account.destroy');
        });
    }
}
