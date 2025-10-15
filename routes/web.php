<?php

use Illuminate\Support\Facades\Route;

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

// Fallback 404 - luôn đặt cuối cùng
Route::fallback(function () {
    return response()->view('client.404', [], 404);
});
