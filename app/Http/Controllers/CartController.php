<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Lấy giỏ hàng của user hoặc tạo mới
    private function getUserCart()
    {
        return Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);
    }

    // Hiển thị giỏ hàng
    public function index()
{
    $cart = $this->getUserCart();   // Lấy giỏ hàng
    $items = $cart->items()->with('product')->get(); // Lấy item + load product

    return view('client.cart', [
        'items' => $items,
        'cart' => $cart
    ]);
}


    // Thêm vào giỏ hàng
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $cart = $this->getUserCart();
        $product = Product::findOrFail($request->product_id);

        // Nếu item đã tồn tại → tăng số lượng
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // Cập nhật số lượng
    public function update(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json(['success' => true]);
    }

    // Xóa item khỏi giỏ
    public function remove($id)
    {
        CartItem::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Đã xóa sản phẩm!');
    }

    // Xóa toàn bộ giỏ
    public function clear()
    {
        $cart = $this->getUserCart();
        $cart->items()->delete();

        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }
}
