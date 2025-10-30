<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
{
    // Lấy 8 sản phẩm mới nhất
    $products = Product::latest()->take(8)->get();

    // Lấy sản phẩm đầu tiên để hiển thị Hero
    $heroProduct = $products->first();

    // Lấy 4 danh mục đầu tiên
    $categories = Category::take(4)->get();

    return view('client.home', compact('products', 'categories', 'heroProduct'));
}

}
