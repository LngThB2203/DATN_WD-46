<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('client.home');
})->name('client.home');


Route::prefix('admin')->group(function () {

    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');

    Route::prefix('products')->group(function () {
        Route::get('/list', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
        Route::get('/grid', fn() => view('admin.products.grid'))->name('products.grid');
        Route::get('/add', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('/add', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
        Route::get('/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/gallery/{gallery}', [App\Http\Controllers\ProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::post('/gallery/{gallery}/set-primary', [App\Http\Controllers\ProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');
    });

    Route::prefix('categories')->group(function () {
        Route::get('/list', fn() => view('admin.categories.list'))->name('categories.list');
        Route::get('/edit', fn() => view('admin.categories.edit'))->name('categories.edit');
        Route::get('/add', fn() => view('admin.categories.add'))->name('categories.add');
    });

    Route::prefix('inventories')->group(function () {
        Route::get('/warehouse', fn() => view('admin.inventories.warehouse'))->name('inventories.warehouse');
        Route::get('/received-orders', fn() => view('admin.inventories.received-orders'))->name('inventories.received-orders');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/list', fn() => view('admin.orders.list'))->name('orders.list');
        Route::get('/show', fn() => view('admin.orders.show'))->name('orders.show');
        Route::get('/cart', fn() => view('admin.orders.cart'))->name('orders.cart');
        Route::get('/checkout', fn() => view('admin.orders.checkout'))->name('orders.checkout');
    });

    Route::prefix('purchases')->group(function () {
        Route::get('/list', fn() => view('admin.purchases.list'))->name('purchases.list');
        Route::get('/order', fn() => view('admin.purchases.order'))->name('purchases.order');
    });

    Route::prefix('attributes')->group(function () {
        Route::get('/list', fn() => view('admin.attributes.list'))->name('attributes.list');
        Route::get('/edit', fn() => view('admin.attributes.edit'))->name('attributes.edit');
        Route::get('/add', fn() => view('admin.attributes.add'))->name('attributes.add');
    });

    Route::prefix('invoices')->group(function () {
        Route::get('/list', fn() => view('admin.invoices.list'))->name('invoices.list');
        Route::get('/show', fn() => view('admin.invoices.show'))->name('invoices.show');
        Route::get('/create', fn() => view('admin.invoices.create'))->name('invoices.create');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/list', fn() => view('admin.roles.list'))->name('roles.list');
        Route::get('/edit', fn() => view('admin.roles.edit'))->name('roles.edit');
        Route::get('/create', fn() => view('admin.roles.create'))->name('roles.create');
    });

    Route::prefix('customers')->group(function () {
        Route::get('/list', fn() => view('admin.customers.list'))->name('customers.list');
        Route::get('/show', fn() => view('admin.customers.show'))->name('customers.show');
    });

    Route::prefix('sellers')->group(function () {
        Route::get('/list', fn() => view('admin.sellers.list'))->name('sellers.list');
        Route::get('/show', fn() => view('admin.sellers.show'))->name('sellers.show');
        Route::get('/edit', fn() => view('admin.sellers.edit'))->name('sellers.edit');
        Route::get('/add', fn() => view('admin.sellers.add'))->name('sellers.add');
    });

    Route::prefix('coupons')->group(function () {
        Route::get('/list', fn() => view('admin.coupons.list'))->name('coupons.list');
        Route::get('/add', fn() => view('admin.coupons.add'))->name('coupons.add');
    });
});
