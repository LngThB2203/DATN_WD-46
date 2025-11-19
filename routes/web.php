<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\StockTransactionController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\WarehouseProductController;
use App\Http\Controllers\Admin\StatisticController;

// ========================
// AUTH ROUTES
// ========================
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');

    // Email verification
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->back()->with('success', 'Email của bạn đã được xác thực!');
    })->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Email xác thực đã được gửi!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Forgot/reset password
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// ========================
// CLIENT ROUTES
// ========================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/category', function () {
    return view('client.category');
})->name('category.index');

Route::get('/product/{slug}', [ProductDetailController::class, 'show'])->name('product.show');
Route::post('/product/{slug}/review', [ReviewController::class, 'store'])->middleware('auth')->name('product.review.store');
Route::get('/product/{slug}/reviews', [ReviewController::class, 'index'])->name('product.reviews.index'); // AJAX phân trang đánh giá

Route::get('/test-cart', fn() => view('client.test-cart'))->name('test.cart');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// API kiểm tra mã giảm giá
Route::post('/api/check-discount', [DiscountController::class, 'checkCode'])->name('api.check-discount');

Route::get('/about', fn() => view('client.about'))->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/faq', fn() => view('client.faq'))->name('faq.index');
Route::get('/privacy', fn() => view('client.privacy'))->name('privacy.index');
Route::get('/tos', fn() => view('client.tos'))->name('tos.index');

// Blog
Route::get('/blog', fn() => view('client.blog'))->name('blog.index');
Route::get('/blog/{slug}', fn($slug) => view('client.blog-details', compact('slug')))->name('blog.show');

// Auth (template combined)
Route::get('/login-register', fn() => view('client.login-register'))->name('auth.index');

// Khác
Route::get('/order-confirmation', fn() => view('client.order-confirmation'))->name('order.confirmation');
Route::get('/payment-methods', fn() => view('client.payment-methods'))->name('payment.methods');
Route::get('/return-policy', fn() => view('client.return-policy'))->name('return.policy');
Route::get('/search', fn() => view('client.search-results'))->name('search.results');
Route::get('/shipping-info', fn() => view('client.shipping-info'))->name('shipping.info');
Route::get('/support', fn() => view('client.support'))->name('support.index');

// ========================
// ADMIN ROUTES
// ========================
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/list', [ProductController::class, 'index'])->name('products.index');
        Route::get('/grid', fn() => view('admin.products.grid'))->name('products.grid');
        Route::get('/add', [ProductController::class, 'create'])->name('products.create');
        Route::post('/add', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/gallery/{gallery}', [ProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::post('/gallery/{gallery}/set-primary', [ProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');
        Route::get('/export/excel', [ProductController::class, 'exportExcel'])->name('products.export-excel');
        Route::get('/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export-pdf');
    });

    // Product Variants
    Route::prefix('variants')->group(function () {
        Route::get('/', [ProductVariantController::class, 'index'])->name('variants.index');
        Route::get('/create', [ProductVariantController::class, 'create'])->name('variants.create');
        Route::post('/', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('variants.edit');
        Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    });

    // Categories
    Route::prefix('categories')->name('admin.categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('list');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AdminCategoryController::class, 'destroy'])->name('destroy');
        Route::get('/toggle/{id}', [AdminCategoryController::class, 'toggleStatus'])->name('toggle');
    });

    // Inventories, Warehouse
    Route::prefix('inventories')->name('inventories.')->group(function () {
        Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse');
        Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.add');
        Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
        Route::get('/warehouse/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('/warehouse/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.update');
        Route::delete('/warehouse/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

        Route::get('/received-orders', [WarehouseProductController::class, 'index'])->name('received-orders');
        Route::put('/received-orders/{id}', [WarehouseProductController::class, 'updateQuantity'])->name('updateQuantity');

        Route::get('/import', [StockTransactionController::class, 'createImport'])->name('import.create');
        Route::post('/import', [StockTransactionController::class, 'storeImport'])->name('import.store');

        Route::get('/export', [StockTransactionController::class, 'createExport'])->name('export.create');
        Route::post('/export', [StockTransactionController::class, 'storeExport'])->name('export.store');

        Route::get('/transactions', [StockTransactionController::class, 'log'])->name('transactions');
        Route::get('/transactions/{id}/print', [StockTransactionController::class, 'printInvoice'])->name('transactions.print');
    });

    // Contacts
    Route::prefix('contacts')->name('admin.contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'adminIndex'])->name('index');
        Route::get('/{contact}', [ContactController::class, 'adminShow'])->name('show');
        Route::post('/{contact}/update-status', [ContactController::class, 'adminUpdateStatus'])->name('update-status');
        Route::post('/{contact}/update-notes', [ContactController::class, 'adminUpdateNotes'])->name('update-notes');
        Route::delete('/{contact}', [ContactController::class, 'adminDestroy'])->name('destroy');
    });

    Route::prefix('banner')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('banner.index');
        Route::get('/create', [BannerController::class, 'create'])->name('banner.create');
        Route::post('/store', [BannerController::class, 'store'])->name('banner.store');
        Route::get('/edit/{banner}', [BannerController::class, 'edit'])->name('banner.edit');
        Route::put('/update/{banner}', [BannerController::class, 'update'])->name('banner.update');
        Route::delete('/delete/{banner}', [BannerController::class, 'destroy'])->name('banner.delete');
        Route::post('/toggle-status/{banner}', [BannerController::class, 'toggleStatus'])
            ->name('banner.toggleStatus');

    });

    Route::prefix('brand')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('brand.index');
        Route::get('/create', [BrandController::class, 'create'])->name('brand.create');
        Route::post('/store', [BrandController::class, 'store'])->name('brand.store');
        Route::get('/edit/{brand}', [BrandController::class, 'edit'])->name('brand.edit');
        Route::post('/update/{brand}', [BrandController::class, 'update'])->name('brand.update');
        Route::get('/delete/{brand}', [BrandController::class, 'destroy'])->name('brand.delete');
        Route::post('/upload-logo/{brand}', [BrandController::class, 'uploadLogo'])->name('brand.uploadLogo');
        Route::get('/{id}/products', [BrandController::class, 'showProducts'])->name('brand.products');
    });

    Route::prefix('discounts')->name('admin.discounts.')->group(function () {
        Route::get('/', [App\Http\Controllers\DiscountController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\DiscountController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\DiscountController::class, 'store'])->name('store');
        Route::get('/{discount}', [App\Http\Controllers\DiscountController::class, 'show'])->name('show');
        Route::get('/{discount}/edit', [App\Http\Controllers\DiscountController::class, 'edit'])->name('edit');
        Route::put('/{discount}', [App\Http\Controllers\DiscountController::class, 'update'])->name('update');
        Route::delete('/{discount}', [App\Http\Controllers\DiscountController::class, 'destroy'])->name('destroy');
    });
// Orders
    Route::prefix('orders')->name('admin.orders.')->middleware('auth')->group(function () {
        Route::get('/list', [OrderController::class, 'index'])->name('list');
        Route::get('/{id}/show', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/update-shipment', [OrderController::class, 'updateShipment'])->name('update-shipment');
    });

    // Purchases
    Route::prefix('purchases')->group(function () {
        Route::get('/list', fn() => view('admin.purchases.list'))->name('purchases.list');
        Route::get('/order', fn() => view('admin.purchases.order'))->name('purchases.order');
    });

    // Attributes
    Route::prefix('attributes')->group(function () {
        Route::get('/list', fn() => view('admin.attributes.list'))->name('attributes.list');
        Route::get('/edit', fn() => view('admin.attributes.edit'))->name('attributes.edit');
        Route::get('/add', fn() => view('admin.attributes.add'))->name('attributes.add');
    });

    // Invoices
    Route::prefix('invoices')->group(function () {
        Route::get('/list', fn() => view('admin.invoices.list'))->name('invoices.list');
        Route::get('/show', fn() => view('admin.invoices.show'))->name('invoices.show');
        Route::get('/create', fn() => view('admin.invoices.create'))->name('invoices.create');
    });

    // Roles
    Route::prefix('roles')->group(function () {
        Route::get('/list', fn() => view('admin.roles.list'))->name('roles.list');
        Route::get('/edit', fn() => view('admin.roles.edit'))->name('roles.edit');
        Route::get('/create', fn() => view('admin.roles.create'))->name('roles.create');
    });

    // Customers
    Route::prefix('customers')->group(function () {
        Route::get('/list', fn() => view('admin.customers.list'))->name('customers.list');
        Route::get('/show', fn() => view('admin.customers.show'))->name('customers.show');
    });

    // Sellers
    Route::prefix('sellers')->group(function () {
        Route::get('/list', fn() => view('admin.sellers.list'))->name('sellers.list');
        Route::get('/show', fn() => view('admin.sellers.show'))->name('sellers.show');
        Route::get('/edit', fn() => view('admin.sellers.edit'))->name('sellers.edit');
        Route::get('/add', fn() => view('admin.sellers.add'))->name('sellers.add');
    });

    // Coupons
    Route::prefix('coupons')->group(function () {
        Route::get('/list', fn() => view('admin.coupons.list'))->name('coupons.list');
        Route::get('/add', fn() => view('admin.coupons.add'))->name('coupons.add');
    });

    // Admin Reviews
    Route::prefix('reviews')->name('admin.reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index');
        Route::get('/create', [AdminReviewController::class, 'create'])->name('create');
        Route::post('/', [AdminReviewController::class, 'store'])->name('store');
        Route::get('/{review}/edit', [AdminReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [AdminReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy');
        Route::post('/{review}/toggle-status', [AdminReviewController::class, 'toggleStatus'])->name('toggle');
    });

    
});

// Fallback 404 - luôn đặt cuối cùng
Route::fallback(function () {
    return response()->view('client.404', [], 404);
});
