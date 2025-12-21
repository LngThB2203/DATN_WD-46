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
        ]);

        try {
            $product   = Product::findOrFail($request->product_id);
            $variantId = $request->variant_id ? (int) $request->variant_id : null;
            $quantity  = (int) $request->quantity;

            $price = $product->sale_price ?? $product->price;
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant && $variant->price_adjustment) {
                    $price += (float) $variant->price_adjustment;
                }
            }

            DB::beginTransaction();

            $cart = $this->getOrCreateCart($request);

            if ($request->user() && ! $cart->user_id) {
                $cart->user_id = $request->user()->id;
                $cart->save();
            }

            $item = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('variant_id', $variantId)
                ->first();

            if ($item) {
                $item->quantity += $quantity;
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
            $cartItem           = CartItem::findOrFail($request->cart_item_id);
            $cartItem->quantity = (int) $request->quantity;
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

            return back()->with('success', 'Đã cập nhật giỏ hàng!');
        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cập nhật thất bại!']);
            }
            return back()->with('error', 'Cập nhật thất bại!');
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
                return response()->json(['success' => true, 'cart_count' => $cartCount]);
            }
            return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
        } catch (\Exception $e) {
            Log::error('Cart remove error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false]);
            }

            return back()->with('error', 'Xóa sản phẩm thất bại!');
        }
    }

    public function clear(Request $request)
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $cart->items()->delete();
            $this->syncCartToSession($request, $cart);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'cart_count' => 0]);
            }

            return back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false]);
            }

            return back()->with('error', 'Xóa giỏ hàng thất bại!');
        }
    }
private function prepareCart(Request $request): array
{
    $cart  = $this->getOrCreateCart($request);
    $items = $cart->items()->with(['product.galleries', 'variant.size', 'variant.scent', 'variant.concentration'])->get();

    $sessionItems = [];
    foreach ($items as $item) {
        $product = $item->product;
        if (!$product) continue;

        $variant = $item->variant;
        $variantName = [];
        if ($variant) {
            if ($variant->size) $variantName['Size'] = $variant->size->size_name;
            if ($variant->scent) $variantName['Scent'] = $variant->scent->scent_name;
            if ($variant->concentration) $variantName['Concentration'] = $variant->concentration->concentration_name;
        }

        $sessionItems[] = [
            'cart_item_id' => $item->id,
            'product_id'   => $item->product_id,
            'variant_id'   => $item->variant_id,
            'quantity'     => max(1, (int) $item->quantity),
            'price'        => (float) $item->price,
            'subtotal'     => $item->quantity * $item->price,
            'name'         => $product->name,
            'variant_name' => $variantName, // lưu dạng mảng
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

private function syncCartToSession(Request $request, Cart $cart): void
{
    $items = $cart->items()->with(['product', 'variant.size', 'variant.scent', 'variant.concentration'])->get();
    $sessionItems = [];

    foreach ($items as $item) {
        $product = $item->product;
        $variant = $item->variant;
        $variantName = [];
        if ($variant) {
            if ($variant->size) $variantName['Size'] = $variant->size->size_name;
            if ($variant->scent) $variantName['Scent'] = $variant->scent->scent_name;
            if ($variant->concentration) $variantName['Concentration'] = $variant->concentration->concentration_name;
        }

        $sessionItems[] = [
            'cart_item_id' => $item->id,
            'product_id'   => $item->product_id,
            'variant_id'   => $item->variant_id,
            'quantity'     => $item->quantity,
            'price'        => (float) $item->price,
            'subtotal'     => $item->quantity * $item->price,
            'name'         => $product->name ?? 'Sản phẩm đã bị xóa',
            'variant_name' => $variantName,
            'image'        => $product?->primaryImage()?->image_path,
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


    private function getOrCreateCart(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()?->id]);
        return $cart;
    }
}
