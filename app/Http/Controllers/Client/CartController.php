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
    /**
     * Lấy giỏ hàng của user hiện tại hoặc tạo mới
     *
     * @return Cart|null
     */
    private function getUserCart(): ?Cart
    {
        if (! Auth::check()) {
            return null;
        }

        return Cart::firstOrCreate([
            'user_id' => Auth::id(),
        ]);
    }

    //Hiển thị trang giỏ hàng
    public function index()
    {
        $cart = $this->getUserCart();

        // Null-safe, tránh lỗi Intelephense
        $items = $cart?->items()->with(['product', 'variant'])->get() ?? collect();

        $total = $items->sum(fn(CartItem $item) => $item->quantity * $item->price);

        return view('client.cart', compact('items', 'total'));
    }

    //Thêm sản phẩm vào giỏ
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cart = $this->getUserCart();
        if (! $cart) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập!'], 401);
        }

        $product = Product::findOrFail($request->product_id);
        $variant = $request->variant_id ? ProductVariant::find($request->variant_id) : null;

        // Giá sản phẩm: nếu có sale_price dùng sale_price, cộng price_adjustment của biến thể
        $price = ($product->sale_price ?? $product->price)
             + ($variant->price_adjustment ?? 0);

        // Kiểm tra xem item đã tồn tại trong giỏ chưa
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity'   => $request->quantity,
                'price'      => $price,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    //Cập nhật số lượng sản phẩm trong giỏ (AJAX)
    public function update(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item           = CartItem::findOrFail($request->item_id);
        $item->quantity = $request->quantity;
        $item->save();

        $cart      = $this->getUserCart();
        $cartTotal = $cart?->items()->sum(fn(CartItem $i) => $i->quantity * $i->price) ?? 0;

        return response()->json([
            'success'    => true,
            'item_total' => $item->quantity * $item->price,
            'cart_total' => $cartTotal,
        ]);
    }

    // /Xóa 1 item khỏi giỏ
    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);

        CartItem::findOrFail($request->item_id)->delete();

        return response()->json(['success' => true]);
    }

    // Xóa toàn bộ giỏ hàng
    public function clear()
    {
        $cart = $this->getUserCart();
        $cart?->items()->delete();

        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }
}
