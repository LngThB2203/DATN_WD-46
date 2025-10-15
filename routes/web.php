<?php

use Illuminate\Support\Facades\Route;

Route::get('/category', function () {
    return view('admin.categories.add');
});
