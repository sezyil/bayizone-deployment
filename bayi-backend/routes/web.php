<?php

use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Frontend\CustomerOrderInvoiceController;
use App\Http\Controllers\Frontend\ProformaInvoiceController;
use App\Libraries\RouteGenerator\Admin\AdminRouteGenerator;
use App\Services\Client\Product\DummyProductService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

AdminRouteGenerator::generate();
/* test */
if (env('APP_ENV') == 'local') {
    Route::get('/test-dummy', function () {
        (new DummyProductService('9c36dcfb-7a19-4d29-95c4-a1ccb6111aaa'))->create();
    });
}



//order invoice
Route::get('/customer-order/preview/{orderId}', [CustomerOrderInvoiceController::class, 'index'])->name('customer-order.invoice');

//proforma invoice
Route::get('/proforma-preview/{proformaId}/{userId}', [ProformaInvoiceController::class, 'index'])->name('proforma-invoice.index');

//proforma invoice password
Route::get('/proforma-approval/{proformaId}', [ProformaInvoiceController::class, 'forCustomer'])->name('proforma-invoice.password');
//proforma approve or reject
Route::post('/proforma-approval/{proformaId}', [ProformaInvoiceController::class, 'approval'])->name('proforma-invoice.approval');

Route::get('/verify-email/{userId}', [AuthController::class, 'verifyEmail'])->name('verify-email');

//reset password email post route
Route::get('/reset-password/{userId}/{token}', function ($userId, $token) {
    $user = \App\Models\User::whereId($userId)->first();
    if ($user) {
        if ($user->reset_password_token == $token) {
            $randomPassword = Str::random(12);
            $user->password = bcrypt($randomPassword);
        } else {
            return '<script>alert("Şifre sıfırlama bağlantısı geçersiz.");window.location.href="https://app.bayizone.com";</script>';
        }
    } else {
        return '<script>alert("Şifre sıfırlama bağlantısı geçersiz.");window.location.href="https://app.bayizone.com";</script>';
    }
})->name('reset-password');
