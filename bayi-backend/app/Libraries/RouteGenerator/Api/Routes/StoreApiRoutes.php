<?php

namespace App\Libraries\RouteGenerator\Api\Routes;

use App\Http\Controllers\Store\StoreController;
use Route;

class StoreApiRoutes
{
    public static function generate()
    {
        Route::prefix('store/{customer_id}')->name('store.')->group(
            function () {
                Route::get('/details', [StoreController::class, 'storeDetail'])->name('details');
                Route::get('/products', [StoreController::class, 'allProducts'])->name('products');
                Route::get('/product/{product_id}', [StoreController::class, 'productDetail'])->name('product.detail');
                Route::get('/categories', [StoreController::class, 'categories'])->name('categories');
                Route::prefix('cart')->name('cart.')->group(
                    function () {
                        Route::get('/', [StoreController::class, 'getCart'])->name('get');
                        Route::delete('/clear', [StoreController::class, 'clearCart'])->name('clear');
                        Route::post('/{product_id}', [StoreController::class, 'addToCart'])->name('add');
                        Route::delete('/{item_id}', [StoreController::class, 'removeFromCart'])->name('remove');
                        Route::put('/{product_id}', [StoreController::class, 'updateCart'])->name('update');
                    }
                );
                Route::post('/offer', [StoreController::class, 'createOffer'])->name('offer');
            }
        );
    }
}
