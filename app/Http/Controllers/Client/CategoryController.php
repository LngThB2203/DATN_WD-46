<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Hiển thị tất cả categories
    public function index()
    {
        $categories = Category::all();
        return view('client.category', compact('categories'));
    }

    // Hiển thị chi tiết 1 category và sản phẩm của nó
    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return view('client.category-show', compact('category'));
    }
}
