<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->prepareCart($request);
        return view('client.cart', compact('cart'));
    }

   public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'variant_id' => 'nullable|exists:product_variants,id',
        'quantity'   => 'required|integer|min:1|max:100',
        'buy_now'    => 'nullable|boolean',
    ]);

    try {
        $product   = Product::findOrFail($request->product_id);
        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $quantity  = (int) $request->quantity;

        $price   = $product->sale_price ?? $product->price;
        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant && $variant->price_adjustment) {
                $price += (float) $variant->price_adjustment;
            }
        }

        DB::beginTransaction();

        $cart = $this->getOrCreateCart($request);

        // Nếu user đã login mà cart.user_id null, cập nhật cart.user_id
        if ($request->user() && !$cart->user_id) {
            $cart->user_id = $request->user()->id;
            $cart->save();
        }

        // Tìm item theo product_id + variant_id
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variantId)
            ->first();

        // Chuẩn hóa dữ liệu cũ: nếu trong DB đã có quantity > 10 thì ép về 10
        $maxPerItem = 10;
        if ($item && (int) $item->quantity > $maxPerItem) {
            $item->quantity = $maxPerItem;
            $item->save();
        }

        $currentItemQty = $item ? (int) $item->quantity : 0;
        $newItemTotal   = $currentItemQty + $quantity;

        // 1) Giới hạn tối đa 10 / mỗi sản phẩm (tính theo tổng cho sản phẩm đó trong giỏ)
        if ($newItemTotal > $maxPerItem) {
            DB::rollBack();
            return back()->withErrors([
                'quantity' => 'Số lượng tối đa cho mỗi sản phẩm là 10.',
            ]);
        }

        // 3) Kiểm tra tồn kho theo tổng cho sản phẩm / biến thể đó
        $stock = null;
        if ($variant) {
            $stock = $variant->stock;
        } elseif (property_exists($product, 'stock') && $product->stock !== null) {
            $stock = $product->stock;
        }

        if ($stock !== null && $newItemTotal > $stock) {
            DB::rollBack();
            return back()->withErrors([
                'quantity' => 'Sản phẩm hiện chỉ còn ' . (int) $stock . ' trong kho.',
            ]);
        }

       if ($item) {
    $item->quantity = $newItemTotal; // tổng mới cho sản phẩm đó
    $item->save();
} else {
    $item = CartItem::create([
        'cart_id'    => $cart->id,
        'product_id' => $product->id,
        'variant_id' => $variantId,
        'quantity'   => $quantity,
        'price'      => $price,
        'added_at'   => now(),
    ]);
}

        $this->syncCartToSession($request, $cart);

        DB::commit();

        // Flow Mua ngay
        if ($request->boolean('buy_now') && $item) {
            return redirect()->route('checkout.index', [
                'selected_items' => $item->id,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Cart add error: ' . $e->getMessage());
        return back()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm.');
    }
}

    public function update(Request $request)
{
    $request->validate([
        'cart_item_id' => 'required|exists:cart_items,id',
        'quantity'     => 'required|integer|min:1|max:100',
    ]);

    try {
        $cartItem = CartItem::findOrFail($request->cart_item_id);
        $quantity = (int) $request->quantity;

        // Chuẩn hóa dữ liệu cũ: nếu trong DB đã có quantity > 10 thì ép về 10
        $maxPerItem = 10;
        if ((int) $cartItem->quantity > $maxPerItem) {
            $cartItem->quantity = $maxPerItem;
            $cartItem->save();
        }

        // Giới hạn tối đa 10 / sản phẩm / đơn
        if ($quantity > $maxPerItem) {
            $message = 'Số lượng tối đa cho mỗi sản phẩm là 10.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['quantity' => $message]);
        }

        $product = $cartItem->product;
        $variant = $cartItem->variant;

        // Kiểm tra tồn kho theo từng sản phẩm / biến thể
        if ($variant) {
            if ($quantity > $variant->stock) {
                $message = 'Sản phẩm biến thể hiện chỉ còn ' . (int) $variant->stock . ' trong kho.';
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withErrors(['quantity' => $message]);
            }
        } elseif ($product && property_exists($product, 'stock') && $product->stock !== null) {
            if ($quantity > $product->stock) {
                $message = 'Sản phẩm hiện chỉ còn ' . (int) $product->stock . ' trong kho.';
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withErrors(['quantity' => $message]);
            }
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        $cart = $cartItem->cart;
        $this->syncCartToSession($request, $cart);

        if ($request->ajax()) {
            $cartCount = $cart->items()->count();
            $cartData  = $this->prepareCart($request);
            return response()->json([
                'success'    => true,
                'message'    => 'Đã cập nhật số lượng!',
                'cart'       => $cartData,
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Cập nhật số lượng thành công!');
    } catch (\Exception $e) {
        Log::error('Cart update error: ' . $e->getMessage());
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật.']);
        }
        return back()->with('error', 'Có lỗi xảy ra khi cập nhật.');
    }
}

    public function remove(Request $request)
    {
        $request->validate(['cart_item_id' => 'required|exists:cart_items,id']);
        try {
            $cartItem = CartItem::findOrFail($request->cart_item_id);
            $cart     = $cartItem->cart;
            $cartItem->delete();

            $this->syncCartToSession($request, $cart);

            if ($request->ajax()) {
                $cartCount = $cart->items()->count();
                $cartData  = $this->prepareCart($request);
                return response()->json([
                    'success'    => true,
                    'message'    => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                    'cart'       => $cartData,
                    'cart_count' => $cartCount,
                ]);
            }
            return back()->with('success', 'Xóa sản phẩm thành công!');
        } catch (\Exception $e) {
            Log::error('Cart remove error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa.']);
            }
            return back()->with('error', 'Có lỗi xảy ra khi xóa.');
        }
    }

    public function clear(Request $request)
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $cart->items()->delete();
            $request->session()->forget('cart');
            $request->session()->forget('cart_id');

            if ($request->ajax()) {
                return response()->json([
                    'success'    => true,
                    'message'    => 'Đã xóa toàn bộ giỏ hàng!',
                    'cart_count' => 0,
                ]);
            }
            return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng!');
        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa.']);
            }
            return back()->with('error', 'Có lỗi xảy ra khi xóa.');
        }
    }

    private function getOrCreateCart(Request $request): Cart
    {
        $user   = $request->user();
        $cartId = $request->session()->get('cart_id');
        $cart   = $cartId ? Cart::find($cartId) : null;

        if ($cart) {
            if ($user && $cart->user_id !== $user->id) {
                $cart->user_id = $user->id;
                $cart->save();
            }
            return $cart;
        }

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            $cart = Cart::create(['user_id' => null]);
        }

        $request->session()->put('cart_id', $cart->id);
        return $cart;
    }

    private function syncCartToSession(Request $request, Cart $cart): void
    {
        $items        = $cart->items()->with(['product', 'variant.size', 'variant.scent', 'variant.concentration'])->get();
        $sessionItems = [];
        $maxPerItem   = 10;

        foreach ($items as $item) {
            $product     = $item->product;
            $variant     = $item->variant;
            $variantName = '';
            if ($variant) {
                $parts = [];
                if ($variant->size) {
                    $parts[] = 'Kích thước: ' . $variant->size->size_name;
                }

                if ($variant->scent) {
                    $parts[] = 'Mùi hương: ' . $variant->scent->scent_name;
                }

                if ($variant->concentration) {
                    $parts[] = 'Nồng độ: ' . $variant->concentration->concentration_name;
                }

                $variantName = implode(' • ', $parts);
            }
            $image = $product ? $product->primaryImage()?->image_path : null;

            $qty = (int) $item->quantity;
            $qty = max(1, min($maxPerItem, $qty));

            $sessionItems[] = [
                'cart_item_id' => $item->id,
                'product_id'   => $item->product_id,
                'variant_id'   => $item->variant_id,
                'quantity'     => $qty,
                'price'        => (float) $item->price,
                'subtotal'     => $qty * $item->price,
                'name'         => $product->name ?? 'Sản phẩm đã bị xóa',
                'variant_name' => $variantName,
                'image'        => $image,
            ];
        }

        $subtotal      = collect($sessionItems)->sum('subtotal');
        $shippingFee   = 30000;
        $discountTotal = (float) ($request->session()->get('cart.discount_total', 0));
        $grandTotal    = max(($subtotal + $shippingFee) - $discountTotal, 0);

        $request->session()->put('cart', [
            'items'          => $sessionItems,
            'subtotal'       => $subtotal,
            'shipping_fee'   => $shippingFee,
            'discount_total' => $discountTotal,
            'grand_total'    => $grandTotal,
        ]);
    }

    public function getCount(Request $request)
    {
        try {
            $cart  = $this->getOrCreateCart($request);
            $count = $cart->items()->count();
            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            Log::error('Cart count error: ' . $e->getMessage());
            return response()->json(['success' => false, 'count' => 0]);
        }
    }

    private function prepareCart(Request $request): array
    {
        $cart  = $this->getOrCreateCart($request);
        $items = $cart->items()->with(['product.galleries', 'variant.size', 'variant.scent', 'variant.concentration'])->get();

        $sessionItems = [];
        $maxPerItem   = 10;
        foreach ($items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }

            $variant     = $item->variant;
            $variantName = '';
            if ($variant) {
                $parts = [];
                if ($variant->size) {
                    $parts[] = 'Kích thước: ' . $variant->size->size_name;
                }

                if ($variant->scent) {
                    $parts[] = 'Mùi hương: ' . $variant->scent->scent_name;
                }

                if ($variant->concentration) {
                    $parts[] = 'Nồng độ: ' . $variant->concentration->concentration_name;
                }

                $variantName = implode(' • ', $parts);
            }

            $qty = (int) $item->quantity;
            $qty = max(1, min($maxPerItem, $qty));

            $sessionItems[] = [
                'cart_item_id' => $item->id,
                'product_id'   => $item->product_id,
                'variant_id'   => $item->variant_id,
                'quantity'     => $qty,
                'price'        => (float) $item->price,
                'subtotal'     => $qty * $item->price,
                'name'         => $product->name,
                'variant_name' => $variantName,
                'image'        => $product->primaryImage()?->image_path,
            ];
        }

        $subtotal      = collect($sessionItems)->sum('subtotal');
        $shippingFee   = 30000;
        $discountTotal = (float) ($request->session()->get('cart.discount_total', 0));
        $grandTotal    = max(($subtotal + $shippingFee) - $discountTotal, 0);

        return [
            'items'          => $sessionItems,
            'subtotal'       => $subtotal,
            'shipping_fee'   => $shippingFee,
            'discount_total' => $discountTotal,
            'grand_total'    => $grandTotal,
        ];
    }
}
