<?php

namespace App\Http\Controllers\Client;

use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
{
    // Lấy 8 sản phẩm mới nhất
    $products = Product::latest()->take(8)->get();

    // Lấy sản phẩm đầu tiên để hiển thị Hero
    $heroBanner = Banner::latest('created_at')->first();

    // Lấy 4 danh mục đầu tiên
    $categories = Category::take(4)->get();

    return view('client.home', compact('products', 'categories', 'heroBanner'));
}

}
