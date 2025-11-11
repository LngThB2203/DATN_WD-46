<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['galleries', 'category'])->where('slug', $slug)->firstOrFail();

        return view('client.product', [
            'product' => $product,
            'galleries' => $product->galleries,
        ]);
    }
}
