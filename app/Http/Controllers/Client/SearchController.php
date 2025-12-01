<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('query');

        if (!$keyword) {
            return view('client.search-results', [
                'products' => collect(),
                'keyword'  => ''
            ]);
        }

        // Tìm theo: name, slug, description, sku
        $products = Product::with('primaryImage')
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('slug', 'LIKE', "%{$keyword}%")
            ->orWhere('sku', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->paginate(12);

        return view('client.search-results', [
            'products' => $products,
            'keyword'  => $keyword,
        ]);
    }
    public function ajaxSearch(Request $request)
{
    $keyword = $request->query('q');

    $products = Product::with('primaryImage')
        ->where('name', 'LIKE', "%{$keyword}%")
        ->orWhere('slug', 'LIKE', "%{$keyword}%")
        ->take(10)
        ->get();

    return response()->json($products);
}

}
