<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Helpers\OrderStatusHelper;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReviewController extends Controller
{
    protected int $maxDays = 15;

    protected function canReview(Order $order, Product $product): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        if ($order->user_id !== $user->id) {
            return false;
        }

        if (OrderStatusHelper::mapOldStatus($order->order_status) !== OrderStatusHelper::COMPLETED) {
            return false;
        }

        if (! $order->completed_at) {
            return false;
        }

        if (now()->diffInDays($order->completed_at) > $this->maxDays) {
            return false;
        }

        if (! $order->details()->where('product_id', $product->id)->exists()) {
            return false;
        }

        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('order_id', $order->id)
            ->exists();

        return ! $alreadyReviewed;
    }

    public function create(Order $order, Product $product)
    {
        abort_unless($this->canReview($order, $product), 403);

        return view('client.orders.review', compact('order', 'product'));
    }

    public function store(Request $request, Order $order, Product $product)
    {
        abort_unless($this->canReview($order, $product), 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
            'order_id'   => $order->id,
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null,
            'status'     => 1,
        ]);

        return redirect()
            ->route('orders.show', $order->id)
            ->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
