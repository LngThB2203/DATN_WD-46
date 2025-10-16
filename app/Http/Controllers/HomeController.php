<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
{
    $query = Product::query()->with('category')->where('status', true);

    $filters = $request->only(['q', 'category', 'min_price', 'max_price']);

    // 🔍 Tìm kiếm theo tên, mô tả, mùi hương, size
    if ($search = $request->input('q')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%")
              ->orWhere('scent', 'like', "%$search%")
              ->orWhere('size', 'like', "%$search%");
        });
    }

    // 🏷️ Lọc theo danh mục
    if ($request->filled('category')) {
        $query->where('category_id', $request->integer('category'));
    }

    // 💰 Lọc theo giá
    if ($request->filled('min_price')) {
        $query->where('price', '>=', (float) $request->input('min_price'));
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', (float) $request->input('max_price'));
    }

    // 🛍️ Lấy sản phẩm
    $products = $query->latest('created_at')->paginate(12)->withQueryString();

    // 📂 Lấy danh mục có đếm sản phẩm
    $categories = Category::withCount('products')->orderBy('name')->get();

    // 🖼️ Banner
    $today = now()->toDateString();
    $banners = Banner::query()
        ->latest('created_at')
        ->take(5)
        ->get();

    // ✅ Trả về view
    return view('client.home', [
        'products' => $products,
        'categories' => $categories,
        'banners' => $banners,
        'filters' => [
            'q' => $request->input('q'),
            'category' => $request->input('category'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
        ],
    ]);


    }
}


