<?php
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\ClientBlogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Admin\StockTransactionController;
use App\Http\Controllers\Admin\TrashController;
use App\Http\Controllers\Admin\WarehouseBatchController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\WarehouseProductController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Client\OrderReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Client\CategoryController as ClientCategoryController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\ProductDetailController;
use App\Http\Controllers\Client\ProductListingController;
use App\Http\Controllers\Client\VNPayController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/test-form', function () {
    return '<form method="POST" action="/test-form-submit">'
    . csrf_field()
        . '<button type="submit">Submit</button>'
        . '</form>';
});


Route::post('/test-form-submit', function () {
    return 'Form submitted successfully!';
});


// ========================
// AUTH ROUTES
// ========================
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// Account & Email Verification
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


// Forgot/Reset Password
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');


// ========================
// CLIENT ROUTES
// ========================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('home.search');
// Category
Route::get('/category', [ClientCategoryController::class, 'index'])->name('category.index');
Route::get('/category/{slug}', [ClientCategoryController::class, 'show'])->name('category.show');


// Product
Route::get('/products', [ProductListingController::class, 'index'])->name('client.products.index');
Route::get('/product/{slug}', [ProductDetailController::class, 'show'])->name('product.show');
Route::post('/product/{slug}/review', [ReviewController::class, 'store'])->middleware('auth')->name('product.review.store');
Route::get('/product/{slug}/reviews', [ReviewController::class, 'index'])->name('product.reviews.index'); // AJAX phân trang đánh giá


// Wishlist
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});


// CART
Route::middleware('auth')->group(function () {
    // CART
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'getCount'])->name('cart.count');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    // CHECKOUT
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Payment
Route::get('/payment/vnpay', [VNPayController::class, 'createPayment'])->name('vnpay.create');      // redirect user tới VNPay
Route::get('/payment/vnpay/return', [VNPayController::class, 'vnpayReturn'])->name('vnpay.return'); // user quay lại
Route::post('/payment/vnpay/ipn', [VNPayController::class, 'vnpayIpn'])->name('vnpay.ipn');

// Orders (Client)
Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [ClientOrderController::class, 'show'])->name('orders.show');
Route::put('/orders/{id}/update-shipping', [ClientOrderController::class, 'updateShipping'])->name('orders.update-shipping');
Route::put('/orders/{id}/cancel', [ClientOrderController::class, 'cancel'])->name('orders.cancel');
Route::put('/orders/{id}/confirm-received', [ClientOrderController::class, 'confirmReceived'])->middleware('auth')->name('orders.confirm-received');

Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/review/{product}', [OrderReviewController::class, 'create'])->name('orders.review.form');
    Route::post('/orders/{order}/review/{product}', [OrderReviewController::class, 'store'])->name('orders.review.store');
});


// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');


// Discounts (Client)
Route::get('/vouchers', [DiscountController::class, 'index'])->name('client.vouchers.index');
Route::get('/my-vouchers', [DiscountController::class, 'myVouchers'])
    ->middleware('auth')
    ->name('client.vouchers.my');
Route::post('/vouchers/save', [DiscountController::class, 'saveForUser'])
    ->middleware('auth')
    ->name('client.vouchers.save');


// Discount API
Route::post('/api/check-discount', [DiscountController::class, 'checkCode'])->name('api.check-discount');
Route::post('/api/apply-discount', [DiscountController::class, 'apply'])->name('api.apply-discount');


// Static Pages
Route::get('/about', fn() => view('client.about'))->name('about');
Route::get('/faq', fn() => view('client.faq'))->name('faq.index');
Route::get('/privacy', fn() => view('client.privacy'))->name('privacy.index');
Route::get('/tos', fn() => view('client.tos'))->name('tos.index');
Route::get('/login-register', fn() => view('client.login-register'))->name('auth.index');
Route::get('/order-confirmation', fn() => view('client.order-confirmation'))->name('order.confirmation');
Route::get('/payment-methods', fn() => view('client.payment-methods'))->name('payment.methods');
Route::get('/return-policy', fn() => view('client.return-policy'))->name('return.policy');
// Route::get('/search', fn() => view('client.search-results'))->name('search.results');
Route::get('/shipping-info', fn() => view('client.shipping-info'))->name('shipping.info');
Route::get('/support', fn() => view('client.support'))->name('support.index');


// Blog
Route::get('/blog', [ClientBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [ClientBlogController::class, 'show'])->name('blog.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Chat AI
Route::get('/chat/messages', [ChatbotController::class, 'fetchMessages']);
Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);


// ========================
// ADMIN ROUTES
// ========================
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn() => redirect()->route('admin.statistics.index'))->name('admin.dashboard');

    // Trash
    Route::get('/trash', [TrashController::class, 'index'])->name('admin.trash.index');

    // Statistics
    Route::prefix('statistics')->name('admin.statistics.')->group(function () {
        Route::get('/', [StatisticController::class, 'index'])->name('index');
        Route::get('/revenue-data', [StatisticController::class, 'revenueData'])->name('revenue-data');
        Route::get('/top-products', [StatisticController::class, 'topProducts'])->name('top-products');
        Route::get('/product-stats', [StatisticController::class, 'productStats'])->name('product-stats');
        Route::get('/export/excel', [StatisticController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export/pdf', [StatisticController::class, 'exportPdf'])->name('export-pdf');
    });


    // Products
    Route::prefix('products')->group(function () {
        Route::get('/list', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/grid', fn() => view('admin.products.grid'))->name('products.grid');
        Route::get('/add', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/add', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/{product}', [AdminProductController::class, 'show'])->name('products.show');
        Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/gallery/{gallery}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::post('/gallery/{gallery}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');
        Route::get('/export/excel', [AdminProductController::class, 'exportExcel'])->name('products.export-excel');
        Route::get('/export/pdf', [AdminProductController::class, 'exportPdf'])->name('products.export-pdf');
    });


    // Variants
    Route::prefix('variants')->name('variants.')->group(function () {
        Route::get('/', [ProductVariantController::class, 'index'])->name('index');
        Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
        Route::post('/', [ProductVariantController::class, 'store'])->name('store');
        Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
        Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');
        Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');
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


    // Discounts
    Route::prefix('discounts')->name('admin.discounts.')->group(function () {
        Route::get('/', [AdminDiscountController::class, 'index'])->name('index');
        Route::get('/create', [AdminDiscountController::class, 'create'])->name('create');
        Route::post('/', [AdminDiscountController::class, 'store'])->name('store');
        Route::get('/{discount}', [AdminDiscountController::class, 'show'])->name('show');
        Route::get('/{discount}/edit', [AdminDiscountController::class, 'edit'])->name('edit');
        Route::put('/{discount}', [AdminDiscountController::class, 'update'])->name('update');
        Route::delete('/{discount}', [AdminDiscountController::class, 'destroy'])->name('destroy');
    });


    // Banners
    Route::prefix('banners')->name('banner.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('delete');
        Route::post('/{banner}/toggle', [BannerController::class, 'toggleStatus'])->name('toggleStatus');
    });


    // Brands
    Route::prefix('brands')->name('brand.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('delete');
        Route::get('/{brand}/products', [BrandController::class, 'showProducts'])->name('products');
    });


    // Inventories
    Route::prefix('inventories')->name('inventories.')->group(function () {


        // Warehouse
        Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse');
        Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.add');
        Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
        Route::get('/warehouse/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('/warehouse/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.update');
        Route::delete('/warehouse/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');
        Route::post('/warehouse/{id}/restore', [WarehouseController::class, 'restore'])->name('warehouse.restore');
        Route::delete('/warehouse/{id}/force-delete', [WarehouseController::class, 'forceDelete'])->name('warehouse.force-delete');


        // Stock
        Route::get('/received-orders', [WarehouseProductController::class, 'index'])->name('received-orders');
        Route::put('/received-orders/{id}', [WarehouseProductController::class, 'updateQuantity'])->name('updateQuantity');
        Route::get('/get-variants/{product}', [WarehouseProductController::class, 'getVariants'])->name('getVariants');
        Route::get('/stock/{product}/{variant?}', [WarehouseProductController::class, 'show'])->name('stock.show');


        // Import
        Route::get('/import', [WarehouseBatchController::class, 'createImport'])->name('import.create');
        Route::post('/import', [WarehouseBatchController::class, 'storeImport'])->name('import.store');


        // Export
        Route::get('/export', [WarehouseBatchController::class, 'createExport'])->name('export.create');
        Route::post('/export', [WarehouseBatchController::class, 'storeExport'])->name('export.store');


        // Transactions
        Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions');
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


    // Orders (Admin)
    Route::prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/list', [OrderController::class, 'index'])->name('list');
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('/update-status/{id}', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::put('/update-warehouse/{id}', [OrderController::class, 'updateWarehouse'])->name('update-warehouse');
        Route::post('/update-shipment/{id}', [OrderController::class, 'updateShipment'])->name('update-shipment');
        Route::get('/cart', fn() => view('admin.orders.cart'))->name('cart');
        Route::get('/checkout', fn() => view('admin.orders.checkout'))->name('checkout');
    });


    Route::prefix('newsletters')->name('admin.newsletters.')->group(function () {
        Route::get('/list', [AdminNewsletterController::class, 'index'])->name('list');
        Route::delete('/delete/{id}', [AdminNewsletterController::class, 'destroy'])->name('delete');
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


    Route::prefix('customers')->name('admin.customers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('list');
        Route::get('/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('store');
        Route::get('/edit/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('edit');
        Route::put('/update/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('update');
        Route::delete('/delete/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/export', [App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('export');
        Route::patch('/toggle-user/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'toggleUser']
)->name('toggleUser');


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

    // Posts
    Route::prefix('post')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('post.index');
        Route::get('/create', [PostController::class, 'create'])->name('post.create');
        Route::post('/store', [PostController::class, 'store'])->name('post.store');
        Route::get('/trashed', [PostController::class, 'trashed'])->name('post.trashed');
        Route::post('/trashed/{id}/restore', [PostController::class, 'restore'])->name('post.restore');
        Route::delete('/trashed/{id}/force-delete', [PostController::class, 'forceDelete'])->name('post.force-delete');
        Route::get('/edit/{post}', [PostController::class, 'edit'])->name('post.edit');
        Route::put('/update/{post}', [PostController::class, 'update'])->name('post.update');
        Route::get('/delete/{post}', [PostController::class, 'destroy'])->name('post.delete');
    });
});


// Fallback 404 - luôn đặt cuối cùng
Route::fallback(function () {
    return response()->view('client.404', [], 404);
});


