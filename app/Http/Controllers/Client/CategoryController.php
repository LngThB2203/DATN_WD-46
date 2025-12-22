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

        $query = Product::where('category_id', $category->id)
            ->where('status', 1);

        // Tìm kiếm
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        $sort = request('sort', '');
        if ($sort == 'new') {
            $query->latest();
        } elseif ($sort == 'asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort == 'desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view('client.category', compact('categories', 'category', 'products'));
    }

}
