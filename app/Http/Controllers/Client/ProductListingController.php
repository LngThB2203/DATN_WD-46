<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('primaryImageModel')->active();

        if ($search = $request->get('q')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $sort = $request->get('sort');
        if ($sort === 'new') {
            $query->orderByDesc('created_at');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->orderByDesc('price');
        } else {
            $query->orderByDesc('created_at');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('client.products', [
            'products' => $products,
            'sort' => $sort,
            'search' => $search ?? '',
        ]);
    }
}
