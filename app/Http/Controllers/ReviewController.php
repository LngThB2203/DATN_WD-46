<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $perPage = (int) $request->get('per_page', 5);
        if ($perPage < 1) { $perPage = 5; }
        if ($perPage > 10) { $perPage = 10; }

        $reviews = $product->reviews()
            ->with('user')
            ->where('status', 1)
            ->latest()
            ->paginate($perPage);

        $html = view('client.partials.reviews', ['reviews' => $reviews])->render();

        return response()->json([
            'html' => $html,
            'next_page_url' => $reviews->nextPageUrl(),
        ]);
    }

    public function store(Request $request, string $slug)
    {
        return redirect()->back()->with('error', 'Vui lòng đánh giá sản phẩm trong mục Đơn hàng của tôi sau khi đơn hàng được hoàn thành.');
    }
}

