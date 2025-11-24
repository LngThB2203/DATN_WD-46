<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    // Danh sách tất cả danh mục
    public function index()
    {
        $categories = Category::where('status', 1)->get();
        return view('client.category', [
            'categories' => $categories,
            'category'   => null,
            'products'   => null,
        ]);
    }

    public function show($slug)
    {
        $categories = Category::where('status', 1)->get();
        $category   = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->where('status', 1)
            ->paginate(12);

        return view('client.category', compact('categories', 'category', 'products'));
    }

}
