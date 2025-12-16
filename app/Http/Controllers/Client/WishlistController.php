<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $items = Wishlist::with(['product.galleries'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('client.wishlist', [
            'items' => $items,
        ]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Đã bỏ sản phẩm khỏi danh sách yêu thích.';
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $message = 'Đã thêm sản phẩm vào danh sách yêu thích.';
        }

        return back()->with('success', $message);
    }
}
