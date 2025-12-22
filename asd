[1mdiff --cc app/Http/Controllers/CheckoutController.php[m
[1mindex 715f451,533cdd2..0000000[m
[1m--- a/app/Http/Controllers/CheckoutController.php[m
[1m+++ b/app/Http/Controllers/CheckoutController.php[m
[36m@@@ -79,18 -70,8 +79,12 @@@[m [mclass CheckoutController extends Contro[m
  [m
      public function store(Request $request)[m
      {[m
[31m -        $user = $request->user();[m
[31m -        [m
[32m +          if (!Auth::check()) {[m
[32m +        return redirect()->route('login')[m
[32m +            ->with('error', 'Vui lòng đăng nhập trước khi đặt hàng');[m
[32m +    }[m
[31m- [m
[31m-     //CHẶN USER BỊ KHÓA[m
[31m-     if (Auth::user()->status == 0) {[m
[31m-         return redirect()->route('checkout.index')[m
[31m-             ->with('error', 'Tài khoản của bạn đang bị khóa. Vui lòng liên hệ quản trị viên.');[m
[31m-     }[m
[32m +         $user = $request->user();[m
[32m +[m
          // Nếu đã đăng nhập, bắt buộc lấy tên và email từ tài khoản[m
          if ($user) {[m
              $validated = $request->validate([[m
[36m@@@ -164,8 -148,8 +161,8 @@@[m
          }[m
  [m
          // ===== COD =====[m
[32m+         $lastOrderId = null;[m
[31m -        DB::transaction(function () use ($validated, $cart, $request, $selectedItems, &$lastOrderId) {[m
[32m +        DB::transaction(function () use ($validated, $cart, $request, $selectedItems) {[m
[31m-          $customer = Customer::where('user_id', auth()->id())->first();[m
  [m
              $order = Order::create([[m
                  'user_id'               => optional($request->user())->id,[m
[36m@@@ -288,7 -275,13 +294,13 @@@[m
          });[m
  [m
          $subtotal = $items->sum('subtotal');[m
[31m -        [m
[32m +[m
[32m+         // Only apply discount if a discount_id is set; otherwise reset discount_total to 0[m
[32m+         $discountTotal = 0;[m
[32m+         if ($request->session()->has('cart.discount_id') && $request->session()->get('cart.discount_id')) {[m
[32m+             $discountTotal = $sessionCart['discount_total'] ?? 0;[m
[32m+         }[m
[32m+ [m
          return [[m
              'items'          => $items->values()->all(),[m
              'subtotal'       => $subtotal,[m
[1mdiff --cc resources/views/client/checkout.blade.php[m
[1mindex 467e9dd,a5a7cc4..0000000[m
[1m--- a/resources/views/client/checkout.blade.php[m
[1m+++ b/resources/views/client/checkout.blade.php[m
[36m@@@ -413,17 -428,87 +428,88 @@@[m
                  });[m
          });[m
      }[m
[32m+ [m
[32m+     // Remove discount handler[m
[32m+     const removeBtn = document.getElementById('removeDiscountBtn');[m
[32m+     if (removeBtn) {[m
[32m+         removeBtn.addEventListener('click', function () {[m
[32m+             if (!confirm('Bạn có chắc muốn bỏ mã giảm giá?')) return;[m
[32m+             const btn = this;[m
[32m+             btn.disabled = true;[m
[32m+             fetch('{{ route('api.remove-discount') }}', {[m
[32m+                 method: 'POST',[m
[32m+                 headers: {[m
[32m+                     'Content-Type': 'application/json',[m
[32m+                     'X-CSRF-TOKEN': '{{ csrf_token() }}',[m
[32m+                     'X-Requested-With': 'XMLHttpRequest'[m
[32m+                 },[m
[32m+                 body: JSON.stringify({})[m
[32m+             })[m
[32m+                 .then(res => res.json().then(data => ({ ok: res.ok, data })))[m
[32m+                 .then(({ ok, data }) => {[m
[32m+                     if (ok && data.success) {[m
[32m+                         const cartInfo = data.cart || {};[m
[32m+                         if (messageEl) {[m
[32m+                             messageEl.textContent = data.message || 'Đã bỏ mã giảm giá.';[m
[32m+                             messageEl.className = 'mt-2 small text-success';[m
[32m+                         }[m
[32m+                         const subtotalEl = document.getElementById('checkoutSubtotal');[m
[32m+                         const discountEl = document.getElementById('checkoutDiscount');[m
[32m+                         const shippingEl = document.getElementById('checkoutShipping');[m
[32m+                         const totalEl = document.getElementById('checkoutTotal');[m
[32m+ [m
[32m+                         if (subtotalEl && cartInfo.subtotal !== undefined) subtotalEl.textContent = formatVND(cartInfo.subtotal);[m
[32m+                         if (discountEl) {[m
[32m+                             discountEl.textContent = '- ' + formatVND(cartInfo.discount_total || 0);[m
[32m+                             discountEl.dataset.code = '';[m
[32m+                             discountEl.dataset.amount = 0;[m
[32m+                         }[m
[32m+                         if (shippingEl && cartInfo.shipping_fee !== undefined) shippingEl.textContent = formatVND(cartInfo.shipping_fee);[m
[32m+                         if (totalEl && cartInfo.grand_total !== undefined) totalEl.textContent = formatVND(cartInfo.grand_total);[m
[32m+ [m
[32m+                         const appliedWrapper = document.getElementById('appliedCodeWrapper');[m
[32m+                         if (appliedWrapper) appliedWrapper.style.display = 'none';[m
[32m+                         const appliedCodeEl = document.getElementById('appliedDiscountCode');[m
[32m+                         if (appliedCodeEl) appliedCodeEl.textContent = '';[m
[32m+                     } else {[m
[32m+                         if (messageEl) {[m
[32m+                             messageEl.textContent = data.message || 'Không thể bỏ mã giảm giá.';[m
[32m+                             messageEl.className = 'mt-2 small text-danger';[m
[32m+                         }[m
[32m+                     }[m
[32m+                 })[m
[32m+                 .catch(() => {[m
[32m+                     if (messageEl) {[m
[32m+                         messageEl.textContent = 'Có lỗi xảy ra khi bỏ mã giảm giá.';[m
[32m+                         messageEl.className = 'mt-2 small text-danger';[m
[32m+                     }[m
[32m+                 })[m
[32m+                 .finally(() => {[m
[32m+                     btn.disabled = false;[m
[32m+                 });[m
[32m+         });[m
[32m+     }[m
[32m+ [m
  })();[m
  [m
[31m- function confirmOrder(btn) {[m
[31m-     // Lấy giá và số lượng sản phẩm từ DOM (đã được cập nhật động)[m
[31m-     const grandTotalEl = document.getElementById('cartGrandTotal');[m
[31m-     const totalPrice = grandTotalEl ? grandTotalEl.textContent.trim() : '0 đ';[m
[32m+ const cartItems = @json($cart['items'] ?? []);[m
[32m+ const cartSubtotal = {{ $cart['subtotal'] ?? 0 }};[m
[32m+ const cartDiscount = {{ !empty($cart['discount_code']) ? (int)($cart['discount_total'] ?? 0) : 0 }};[m
[32m+ const cartShipping = {{ $cart['shipping_fee'] ?? 0 }};[m
[32m+ const cartTotal = {{ $cart['grand_total'] ?? 0 }};[m
[32m+ const discountCode = @json($cart['discount_code'] ?? null);[m
  [m
[31m-     // Đếm số lượng sản phẩm từ danh sách items[m
[31m-     const itemCount = document.querySelectorAll('.cart-item-row').length || {{ count($cart['items'] ?? []) }};[m
[31m-     const productCount = itemCount + ' sản phẩm';[m
[32m+ function formatVND(amount) {[m
[32m+     return new Intl.NumberFormat('vi-VN').format(Number(amount || 0)) + ' đ';[m
[32m+ }[m
[32m+ [m
[32m+ function parseNumberFromText(str) {[m
[32m+     if (!str) return 0;[m
[32m+     return Number(String(str).replace(/[^0-9]/g, '')) || 0;[m
[32m+ }[m
[32m+ [m
[32m+ function confirmOrder(btn) {[m
[32m +[m
      let timerInterval;[m
      let countdown = 5;[m
  [m
[1mdiff --cc routes/web.php[m
[1mindex 7e5150e,f8d6d90..0000000[m
[1m--- a/routes/web.php[m
[1m+++ b/routes/web.php[m
[36m@@@ -196,198 -237,242 +237,243 @@@[m [mRoute::post('/chat/send', [ChatbotContr[m
  // ADMIN ROUTES[m
  // ========================[m
  Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {[m
[31m-     Route::get('/', fn() => redirect()->route('admin.statistics.index'))->name('admin.dashboard');[m
[31m- [m
[31m-     // Trash[m
[31m-     Route::get('/trash', [TrashController::class, 'index'])->name('admin.trash.index');[m
[31m- [m
[31m-     // Statistics[m
[31m-     Route::prefix('statistics')->name('admin.statistics.')->group(function () {[m
[31m-         Route::get('/', [StatisticController::class, 'index'])->name('index');[m
[31m-         Route::get('/revenue-data', [StatisticController::class, 'revenueData'])->name('revenue-data');[m
[31m-         Route::get('/top-products', [StatisticController::class, 'topProducts'])->name('top-products');[m
[31m-         Route::get('/product-stats', [StatisticController::class, 'productStats'])->name('product-stats');[m
[31m-         Route::get('/export/excel', [StatisticController::class, 'exportExcel'])->name('export-excel');[m
[31m-         Route::get('/export/pdf', [StatisticController::class, 'exportPdf'])->name('export-pdf');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Products[m
[31m-     Route::prefix('products')->group(function () {[m
[31m-         Route::get('/list', [AdminProductController::class, 'index'])->name('products.index');[m
[31m-         Route::get('/grid', fn() => view('admin.products.grid'))->name('products.grid');[m
[31m-         Route::get('/add', [AdminProductController::class, 'create'])->name('products.create');[m
[31m-         Route::post('/add', [AdminProductController::class, 'store'])->name('products.store');[m
[31m-         Route::get('/{product}', [AdminProductController::class, 'show'])->name('products.show');[m
[31m-         Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');[m
[31m-         Route::put('/{product}', [AdminProductController::class, 'update'])->name('products.update');[m
[31m-         Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');[m
[31m-         Route::delete('/gallery/{gallery}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');[m
[31m-         Route::post('/gallery/{gallery}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');[m
[31m-         Route::get('/export/excel', [AdminProductController::class, 'exportExcel'])->name('products.export-excel');[m
[31m-         Route::get('/export/pdf', [AdminProductController::class, 'exportPdf'])->name('products.export-pdf');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Variants[m
[31m-     Route::prefix('variants')->name('variants.')->group(function () {[m
[31m-         Route::get('/', [ProductVariantController::class, 'index'])->name('index');[m
[31m-         Route::get('/create', [ProductVariantController::class, 'create'])->name('create');[m
[31m-         Route::post('/', [ProductVariantController::class, 'store'])->name('store');[m
[31m-         Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');[m
[31m-         Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');[m
[31m-         Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Categories[m
[31m-     Route::prefix('categories')->name('admin.categories.')->group(function () {[m
[31m-         Route::get('/', [AdminCategoryController::class, 'index'])->name('list');[m
[31m-         Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');[m
[31m-         Route::post('/store', [AdminCategoryController::class, 'store'])->name('store');[m
[31m-         Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('edit');[m
[31m-         Route::put('/update/{id}', [AdminCategoryController::class, 'update'])->name('update');[m
[31m-         Route::delete('/delete/{id}', [AdminCategoryController::class, 'destroy'])->name('destroy');[m
[31m-         Route::get('/toggle/{id}', [AdminCategoryController::class, 'toggleStatus'])->name('toggle');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Discounts[m
[31m-     Route::prefix('discounts')->name('admin.discounts.')->group(function () {[m
[31m-         Route::get('/', [AdminDiscountController::class, 'index'])->name('index');[m
[31m-         Route::get('/create', [AdminDiscountController::class, 'create'])->name('create');[m
[31m-         Route::post('/', [AdminDiscountController::class, 'store'])->name('store');[m
[31m-         Route::get('/{discount}', [AdminDiscountController::class, 'show'])->name('show');[m
[31m-         Route::get('/{discount}/edit', [AdminDiscountController::class, 'edit'])->name('edit');[m
[31m-         Route::put('/{discount}', [AdminDiscountController::class, 'update'])->name('update');[m
[31m-         Route::delete('/{discount}', [AdminDiscountController::class, 'destroy'])->name('destroy');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Banners[m
[31m-     Route::prefix('banners')->name('banner.')->group(function () {[m
[31m-         Route::get('/', [BannerController::class, 'index'])->name('index');[m
[31m-         Route::get('/create', [BannerController::class, 'create'])->name('create');[m
[31m-         Route::post('/store', [BannerController::class, 'store'])->name('store');[m
[31m-         Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');[m
[31m-         Route::put('/{banner}', [BannerController::class, 'update'])->name('update');[m
[31m-         Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('delete');[m
[31m-         Route::post('/{banner}/toggle', [BannerController::class, 'toggleStatus'])->name('toggleStatus');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Brands[m
[31m-     Route::prefix('brands')->name('brand.')->group(function () {[m
[31m-         Route::get('/', [BrandController::class, 'index'])->name('index');[m
[31m-         Route::get('/create', [BrandController::class, 'create'])->name('create');[m
[31m-         Route::post('/store', [BrandController::class, 'store'])->name('store');[m
[31m-         Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');[m
[31m-         Route::put('/{brand}', [BrandController::class, 'update'])->name('update');[m
[31m-         Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('delete');[m
[31m-         Route::get('/{brand}/products', [BrandController::class, 'showProducts'])->name('products');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Inventories[m
[31m-     Route::prefix('inventories')->name('inventories.')->group(function () {[m
[31m- [m
[31m- [m
[31m-         // Warehouse[m
[31m-         Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse');[m
[31m-         Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.add');[m
[31m-         Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');[m
[31m-         Route::get('/warehouse/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');[m
[31m-         Route::put('/warehouse/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.update');[m
[31m-         Route::delete('/warehouse/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');[m
[31m- [m
[31m- [m
[31m-         // Stock[m
[31m-         Route::get('/received-orders', [WarehouseProductController::class, 'index'])->name('received-orders');[m
[31m-         Route::put('/received-orders/{id}', [WarehouseProductController::class, 'updateQuantity'])->name('updateQuantity');[m
[31m-         Route::get('/get-variants/{product}', [WarehouseProductController::class, 'getVariants'])->name('getVariants');[m
[31m- [m
[31m- [m
[31m-         // Import[m
[31m-         Route::get('/import', [WarehouseProductController::class, 'createImport'])->name('import.create');[m
[31m-         Route::post('/import', [WarehouseProductController::class, 'import'])->name('import.store');[m
[31m- [m
[31m- [m
[31m-         // Export[m
[31m-         Route::get('/export', [WarehouseProductController::class, 'createExport'])->name('export.create');[m
[31m-         Route::post('/export', [WarehouseProductController::class, 'export'])->name('export.store');[m
[31m- [m
[31m- [m
[31m-         // Transactions[m
[31m-         Route::get('/transactions', [StockTransactionController::class, 'log'])->name('transactions');[m
[31m-         Route::get('/transactions/{id}/print', [StockTransactionController::class, 'printInvoice'])->name('transactions.print');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Contacts[m
[31m-     Route::prefix('contacts')->name('admin.contacts.')->group(function () {[m
[31m-         Route::get('/', [ContactController::class, 'adminIndex'])->name('index');[m
[31m-         Route::get('/{contact}', [ContactController::class, 'adminShow'])->name('show');[m
[31m-         Route::post('/{contact}/update-status', [ContactController::class, 'adminUpdateStatus'])->name('update-status');[m
[31m-         Route::post('/{contact}/update-notes', [ContactController::class, 'adminUpdateNotes'])->name('update-notes');[m
[31m-         Route::delete('/{contact}', [ContactController::class, 'adminDestroy'])->name('destroy');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Orders (Admin)[m
[31m-     Route::prefix('orders')->name('admin.orders.')->group(function () {[m
[31m-         Route::get('/list', [OrderController::class, 'index'])->name('list');[m
[31m-         Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');[m
[31m-         Route::put('/update-status/{id}', [OrderController::class, 'updateStatus'])->name('update-status');[m
[31m-         Route::put('/update-warehouse/{id}', [OrderController::class, 'updateWarehouse'])->name('update-warehouse');[m
[31m-         Route::post('/update-shipment/{id}', [OrderController::class, 'updateShipment'])->name('update-shipment');[m
[31m-         Route::get('/cart', fn() => view('admin.orders.cart'))->name('cart');[m
[31m-         Route::get('/checkout', fn() => view('admin.orders.checkout'))->name('checkout');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     Route::prefix('newsletters')->name('admin.newsletters.')->group(function () {[m
[31m-         Route::get('/list', [AdminNewsletterController::class, 'index'])->name('list');[m
[31m-         Route::delete('/delete/{id}', [AdminNewsletterController::class, 'destroy'])->name('delete');[m
[31m-     });[m
[31m-     // Purchases[m
[31m-     Route::prefix('purchases')->group(function () {[m
[31m-         Route::get('/list', fn() => view('admin.purchases.list'))->name('purchases.list');[m
[31m-         Route::get('/order', fn() => view('admin.purchases.order'))->name('purchases.order');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Attributes[m
[31m-     Route::prefix('attributes')->group(function () {[m
[31m-         Route::get('/list', fn() => view('admin.attributes.list'))->name('attributes.list');[m
[31m-         Route::get('/edit', fn() => view('admin.attributes.edit'))->name('attributes.edit');[m
[31m-         Route::get('/add', fn() => view('admin.attributes.add'))->name('attributes.add');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Invoices[m
[31m-     Route::prefix('invoices')->group(function () {[m
[31m-         Route::get('/list', fn() => view('admin.invoices.list'))->name('invoices.list');[m
[31m-         Route::get('/show', fn() => view('admin.invoices.show'))->name('invoices.show');[m
[31m-         Route::get('/create', fn() => view('admin.invoices.create'))->name('invoices.create');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     // Roles[m
[31m-     Route::prefix('roles')->group(function () {[m
[31m-         Route::get('/list', fn() => view('admin.roles.list'))->name('roles.list');[m
[31m-         Route::get('/edit', fn() => view('admin.roles.edit'))->name('roles.edit');[m
[31m-         Route::get('/create', fn() => view('admin.roles.create'))->name('roles.create');[m
[31m-     });[m
[31m- [m
[31m- [m
[31m-     Route::prefix('customers')->name('admin.customers.')->group(function () {[m
[31m-         Route::get('/', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('list');[m
[31m-         Route::post('/store', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('store');[m
[31m-         Route::get('/edit/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('edit');[m
[31m-         Route::put('/update/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('update');[m
[31m-         Route::delete('/delete/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('destroy');[m
[31m-         Route::get('/export', [App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('export');[m
[31m-         Route::patch('/toggle-user/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'toggleUser'][m
[32m+    Route::get('/', fn() => redirect()->route('admin.statistics.index'))->name('admin.dashboard');[m
[32m+ [m
[32m+ [m
[32m+    // Trash[m
[32m+    Route::get('/trash', [TrashController::class, 'index'])->name('admin.trash.index');[m
[32m+ [m
[32m+ [m
[32m+    // Statistics[m
[32m+    Route::prefix('statistics')->name('admin.statistics.')->group(function () {[m
[32m+        Route::get('/', [StatisticController::class, 'index'])->name('index');[m
[32m+        Route::get('/revenue-data', [StatisticController::class, 'revenueData'])->name('revenue-data');[m
[32m+        Route::get('/top-products', [StatisticController::class, 'topProducts'])->name('top-products');[m
[32m+        Route::get('/product-stats', [StatisticController::class, 'productStats'])->name('product-stats');[m
[32m+        Route::get('/export/excel', [StatisticController::class, 'exportExcel'])->name('export-excel');[m
[32m+        Route::get('/export/pdf', [StatisticController::class, 'exportPdf'])->name('export-pdf');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Products[m
[32m+    Route::prefix('products')->group(function () {[m
[32m+        Route::get('/list', [AdminProductController::class, 'index'])->name('products.index');[m
[32m+        Route::get('/grid', fn() => view('admin.products.grid'))->name('products.grid');[m
[32m+        Route::get('/add', [AdminProductController::class, 'create'])->name('products.create');[m
[32m+        Route::post('/add', [AdminProductController::class, 'store'])->name('products.store');[m
[32m+        Route::get('/{product}', [AdminProductController::class, 'show'])->name('products.show');[m
[32m+        Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');[m
[32m+        Route::put('/{product}', [AdminProductController::class, 'update'])->name('products.update');[m
[32m+        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');[m
[32m+        Route::delete('/gallery/{gallery}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');[m
[32m+        Route::post('/gallery/{gallery}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');[m
[32m+        Route::get('/export/excel', [AdminProductController::class, 'exportExcel'])->name('products.export-excel');[m
[32m+        Route::get('/export/pdf', [AdminProductController::class, 'exportPdf'])->name('products.export-pdf');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Variants[m
[32m+    Route::prefix('variants')->name('variants.')->group(function () {[m
[32m+        Route::get('/', [ProductVariantController::class, 'index'])->name('index');[m
[32m+        Route::get('/create', [ProductVariantController::class, 'create'])->name('create');[m
[32m+        Route::post('/', [ProductVariantController::class, 'store'])->name('store');[m
[32m+        Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');[m
[32m+        Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');[m
[32m+        Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Categories[m
[32m+    Route::prefix('categories')->name('admin.categories.')->group(function () {[m
[32m+        Route::get('/', [AdminCategoryController::class, 'index'])->name('list');[m
[32m+        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');[m
[32m+        Route::post('/store', [AdminCategoryController::class, 'store'])->name('store');[m
[32m+        Route::get('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('edit');[m
[32m+        Route::put('/update/{id}', [AdminCategoryController::class, 'update'])->name('update');[m
[32m+        Route::delete('/delete/{id}', [AdminCategoryController::class, 'destroy'])->name('destroy');[m
[32m+        Route::get('/toggle/{id}', [AdminCategoryController::class, 'toggleStatus'])->name('toggle');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Discounts[m
[32m+    Route::prefix('discounts')->name('admin.discounts.')->group(function () {[m
[32m+        Route::get('/', [AdminDiscountController::class, 'index'])->name('index');[m
[32m+        Route::get('/create', [AdminDiscountController::class, 'create'])->name('create');[m
[32m+        Route::post('/', [AdminDiscountController::class, 'store'])->name('store');[m
[32m+        Route::get('/{discount}', [AdminDiscountController::class, 'show'])->name('show');[m
[32m+        Route::get('/{discount}/edit', [AdminDiscountController::class, 'edit'])->name('edit');[m
[32m+        Route::put('/{discount}', [AdminDiscountController::class, 'update'])->name('update');[m
[32m+        Route::delete('/{discount}', [AdminDiscountController::class, 'destroy'])->name('destroy');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Banners[m
[32m+    Route::prefix('banners')->name('banner.')->group(function () {[m
[32m+        Route::get('/', [BannerController::class, 'index'])->name('index');[m
[32m+        Route::get('/create', [BannerController::class, 'create'])->name('create');[m
[32m+        Route::post('/store', [BannerController::class, 'store'])->name('store');[m
[32m+        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');[m
[32m+        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');[m
[32m+        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('delete');[m
[32m+        Route::post('/{banner}/toggle', [BannerController::class, 'toggleStatus'])->name('toggleStatus');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Brands[m
[32m+    Route::prefix('brands')->name('brand.')->group(function () {[m
[32m+        Route::get('/', [BrandController::class, 'index'])->name('index');[m
[32m+        Route::get('/create', [BrandController::class, 'create'])->name('create');[m
[32m+        Route::post('/store', [BrandController::class, 'store'])->name('store');[m
[32m+        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');[m
[32m+        Route::put('/{brand}', [BrandController::class, 'update'])->name('update');[m
[32m+        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('delete');[m
[32m+        Route::get('/{brand}/products', [BrandController::class, 'showProducts'])->name('products');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Inventories[m
[32m+    Route::prefix('inventories')->name('inventories.')->group(function () {[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+        // Warehouse[m
[32m+        Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse');[m
[32m+        Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.add');[m
[32m+        Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');[m
[32m+        Route::get('/warehouse/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');[m
[32m+        Route::put('/warehouse/{warehouse}', [WarehouseController::class, 'update'])->name('warehouse.update');[m
[32m+        Route::delete('/warehouse/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');[m
[32m+        Route::post('/warehouse/{id}/restore', [WarehouseController::class, 'restore'])->name('warehouse.restore');[m
[32m+        Route::delete('/warehouse/{id}/force-delete', [WarehouseController::class, 'forceDelete'])->name('warehouse.force-delete');[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m++[m
[32m+        // Stock[m
[32m+        Route::get('/received-orders', [WarehouseProductController::class, 'index'])->name('received-orders');[m
[32m+        Route::put('/received-orders/{id}', [WarehouseProductController::class, 'updateQuantity'])->name('updateQuantity');[m
[32m+        Route::get('/get-variants/{product}', [WarehouseProductController::class, 'getVariants'])->name('getVariants');[m
[32m+        Route::get('/stock/{product}/{variant?}', [WarehouseProductController::class, 'show'])->name('stock.show');[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+        // Import[m
[32m+        Route::get('/import', [WarehouseBatchController::class, 'createImport'])->name('import.create');[m
[32m+        Route::post('/import', [WarehouseBatchController::class, 'storeImport'])->name('import.store');[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+        // Export[m
[32m+        Route::get('/export', [WarehouseBatchController::class, 'createExport'])->name('export.create');[m
[32m+        Route::post('/export', [WarehouseBatchController::class, 'storeExport'])->name('export.store');[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+        // Transactions[m
[32m+        Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions');[m
[32m+        Route::get('/transactions/{id}/print', [StockTransactionController::class, 'printInvoice'])->name('transactions.print');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Contacts[m
[32m+    Route::prefix('contacts')->name('admin.contacts.')->group(function () {[m
[32m+        Route::get('/', [ContactController::class, 'adminIndex'])->name('index');[m
[32m+        Route::get('/{contact}', [ContactController::class, 'adminShow'])->name('show');[m
[32m+        Route::post('/{contact}/update-status', [ContactController::class, 'adminUpdateStatus'])->name('update-status');[m
[32m+        Route::post('/{contact}/update-notes', [ContactController::class, 'adminUpdateNotes'])->name('update-notes');[m
[32m+        Route::delete('/{contact}', [ContactController::class, 'adminDestroy'])->name('destroy');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Orders (Admin)[m
[32m+    Route::prefix('orders')->name('admin.orders.')->group(function () {[m
[32m+        Route::get('/list', [OrderController::class, 'index'])->name('list');[m
[32m+        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');[m
[32m+        Route::put('/update-status/{id}', [OrderController::class, 'updateStatus'])->name('update-status');[m
[32m+        Route::put('/update-warehouse/{id}', [OrderController::class, 'updateWarehouse'])->name('update-warehouse');[m
[32m+        Route::post('/update-shipment/{id}', [OrderController::class, 'updateShipment'])->name('update-shipment');[m
[32m+        Route::get('/cart', fn() => view('admin.orders.cart'))->name('cart');[m
[32m+        Route::get('/checkout', fn() => view('admin.orders.checkout'))->name('checkout');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    Route::prefix('newsletters')->name('admin.newsletters.')->group(function () {[m
[32m+        Route::get('/list', [AdminNewsletterController::class, 'index'])->name('list');[m
[32m+        Route::delete('/delete/{id}', [AdminNewsletterController::class, 'destroy'])->name('delete');[m
[32m+    });[m
[32m+    // Purchases[m
[32m+    Route::prefix('purchases')->group(function () {[m
[32m+        Route::get('/list', fn() => view('admin.purchases.list'))->name('purchases.list');[m
[32m+        Route::get('/order', fn() => view('admin.purchases.order'))->name('purchases.order');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Attributes[m
[32m+    Route::prefix('attributes')->group(function () {[m
[32m+        Route::get('/list', fn() => view('admin.attributes.list'))->name('attributes.list');[m
[32m+        Route::get('/edit', fn() => view('admin.attributes.edit'))->name('attributes.edit');[m
[32m+        Route::get('/add', fn() => view('admin.attributes.add'))->name('attributes.add');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Invoices[m
[32m+    Route::prefix('invoices')->group(function () {[m
[32m+        Route::get('/list', fn() => view('admin.invoices.list'))->name('invoices.list');[m
[32m+        Route::get('/show', fn() => view('admin.invoices.show'))->name('invoices.show');[m
[32m+        Route::get('/create', fn() => view('admin.invoices.create'))->name('invoices.create');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    // Roles[m
[32m+    Route::prefix('roles')->group(function () {[m
[32m+        Route::get('/list', fn() => view('admin.roles.list'))->name('roles.list');[m
[32m+        Route::get('/edit', fn() => view('admin.roles.edit'))->name('roles.edit');[m
[32m+        Route::get('/create', fn() => view('admin.roles.create'))->name('roles.create');[m
[32m+    });[m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+ [m
[32m+    Route::prefix('customers')->name('admin.customers.')->group(function () {[m
[32m+        Route::get('/', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('list');[m
[32m+        Route::get('/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('create');[m
[32m+        Route::post('/store', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('store');[m
[32m+        Route::get('/edit/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('edit');[m
[32m+        Route::put('/update/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('update');[m
[32m+        Route::delete('/delete/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('destroy');[m
[32m+        Route::get('/export', [App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('export');[m
[32m+        Route::patch('/toggle-user/{customer}',[App\Http\Controllers\Admin\CustomerController::class, 'toggleUser'][m
  )->name('toggleUser');[m
  [m
  [m
