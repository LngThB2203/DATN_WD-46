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
        // Tìm sản phẩm theo slug hoặc ID
        $product = Product::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn điểm đánh giá.',
            'rating.integer' => 'Điểm đánh giá phải là số nguyên.',
            'rating.min' => 'Điểm đánh giá tối thiểu là 1.',
            'rating.max' => 'Điểm đánh giá tối đa là 5.',
            'comment.max' => 'Nhận xét không được vượt quá 1000 ký tự.',
        ]);

        // Kiểm tra xem user đã đánh giá sản phẩm này chưa
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            // Cập nhật đánh giá cũ
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return redirect()->back()->with('success', 'Đánh giá của bạn đã được cập nhật!');
        }

        // Tạo đánh giá mới
        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}

