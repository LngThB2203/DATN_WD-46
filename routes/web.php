<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
// Client routes
Route::get('/', function () {
    return view('client.home');
})->name('home');

Route::get('/category', function () {
    return view('client.category');
})->name('category.index');

Route::get('/product/{slug}', function ($slug) {
    return view('client.product', compact('slug'));
})->name('product.show');

Route::get('/cart', function () {
    return view('client.cart');
})->name('cart.index');

Route::get('/checkout', function () {
    return view('client.checkout');
})->name('checkout.index');

// API route để kiểm tra mã giảm giá khi thanh toán
Route::post('/api/check-discount', [App\Http\Controllers\DiscountController::class, 'checkCode'])->name('api.check-discount');

Route::get('/about', function () {
    return view('client.about');
})->name('about');

Route::get('/contact', function () {
    return view('client.contact');
})->name('contact.index');

Route::get('/faq', function () {
    return view('client.faq');
})->name('faq.index');

Route::get('/privacy', function () {
    return view('client.privacy');
})->name('privacy.index');

Route::get('/tos', function () {
    return view('client.tos');
})->name('tos.index');

Route::get('/account', function () {
    return view('client.account');
})->name('account.index');

// Blog
Route::get('/blog', function () {
    return view('client.blog');
})->name('blog.index');

Route::get('/blog/{slug}', function ($slug) {
    return view('client.blog-details', compact('slug'));
})->name('blog.show');

// Auth (template trang kết hợp)
Route::get('/login-register', function () {
    return view('client.login-register');
})->name('auth.index');

// Khác
Route::get('/order-confirmation', function () {
    return view('client.order-confirmation');
})->name('order.confirmation');

Route::get('/payment-methods', function () {
    return view('client.payment-methods');
})->name('payment.methods');

Route::get('/return-policy', function () {
    return view('client.return-policy');
})->name('return.policy');

Route::get('/search', function () {
    return view('client.search-results');
})->name('search.results');

Route::get('/shipping-info', function () {
    return view('client.shipping-info');
})->name('shipping.info');

Route::get('/support', function () {
    return view('client.support');
})->name('support.index');

// Admin sample route (tạm giữ)
Route::get('/admin/category', function () {
    return view('admin.categories.list');
})->name('admin.categories.index');

// Auth routes
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


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
        Route::get('/export/excel', [App\Http\Controllers\ProductController::class, 'exportExcel'])->name('products.export-excel');
        Route::get('/export/pdf', [App\Http\Controllers\ProductController::class, 'exportPdf'])->name('products.export-pdf');
    });

    Route::prefix('categories')->name('admin.categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('list');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AdminCategoryController::class, 'destroy'])->name('destroy');
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

    // Discounts (Mã giảm giá)
    Route::prefix('discounts')->name('admin.discounts.')->group(function () {
        Route::get('/', [App\Http\Controllers\DiscountController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\DiscountController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\DiscountController::class, 'store'])->name('store');
        Route::get('/{discount}', [App\Http\Controllers\DiscountController::class, 'show'])->name('show');
        Route::get('/{discount}/edit', [App\Http\Controllers\DiscountController::class, 'edit'])->name('edit');
        Route::put('/{discount}', [App\Http\Controllers\DiscountController::class, 'update'])->name('update');
        Route::delete('/{discount}', [App\Http\Controllers\DiscountController::class, 'destroy'])->name('destroy');
    });
});

// Fallback 404 - luôn đặt cuối cùng
Route::fallback(function () {
    return response()->view('client.404', [], 404);
});
