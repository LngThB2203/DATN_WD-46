<?php

namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Lấy giỏ hàng theo user
    private function getUserCart() {
        if (!Auth::check()) return null;

        return Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);
    }

    // Hiển thị giỏ
    public function index()
    {
        $cart = $this->getUserCart();
        $items = $cart ? $cart->items()->with(['product', 'variant'])->get() : collect();

        $total = $items->sum(fn($item) => $item->quantity * $item->price);

        return view('client.cart', compact('items', 'total'));
    }

    // Thêm sản phẩm
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'variant_id' => 'nullable',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->getUserCart();

        $product = Product::findOrFail($request->product_id);
        $variant = $request->variant_id ? ProductVariant::find($request->variant_id) : null;

        $price = $variant
            ? ($product->sale_price ?? $product->price) + ($variant->price_adjustment ?? 0)
            : ($product->sale_price ?? $product->price);

        // Kiểm tra tồn tại item
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    // cập nhật số lượng (AJAX)
    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $item = CartItem::findOrFail($request->item_id);
        $item->quantity = $request->quantity;
        $item->save();

        $cart = $this->getUserCart();
        $total = $cart->items->sum(fn($i) => $i->quantity * $i->price);

        return response()->json([
            'success' => true,
            'item_total' => $item->quantity * $item->price,
            'cart_total' => $total
        ]);
    }

    // Xóa item
    public function remove(Request $request)
    {
        CartItem::findOrFail($request->item_id)->delete();

        return response()->json(['success' => true]);
    }

    // Xóa toàn bộ giỏ
    public function clear()
    {
        $cart = $this->getUserCart();
        $cart?->items()->delete();

        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }
}
