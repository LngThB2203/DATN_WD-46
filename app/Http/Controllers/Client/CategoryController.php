<?php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    // Danh sách danh mục
    public function index()
    {
        $categories = Category::where('status', 1)->get();
        return view('client.category', compact('categories'));
    }

    // Sản phẩm theo danh mục
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('status', 1)
            ->paginate(12);

        return view('client.category-products', compact('category', 'products'));
    }
}
