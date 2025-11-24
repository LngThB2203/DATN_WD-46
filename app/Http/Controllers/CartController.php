<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->prepareCart($request);

        return view('client.cart', [
            'cart' => $cart,
        ]);
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'required|integer|min:1|max:100',
            ], [
                'product_id.required' => 'Vui lòng chọn sản phẩm.',
                'product_id.exists' => 'Sản phẩm không tồn tại.',
                'quantity.required' => 'Vui lòng nhập số lượng.',
                'quantity.integer' => 'Số lượng phải là số nguyên.',
                'quantity.min' => 'Số lượng tối thiểu là 1.',
                'quantity.max' => 'Số lượng tối đa là 100.',
            ]);

            $product = Product::findOrFail($request->product_id);
            $quantity = (int) $request->quantity;
            $variantId = $request->variant_id ? (int) $request->variant_id : null;
            
            // Tính giá: nếu có variant thì lấy giá từ variant, không thì lấy từ product
            $price = $product->sale_price ?? $product->price;
            if ($variantId) {
                $variant = \App\Models\ProductVariant::find($variantId);
                if ($variant && $variant->price_adjustment) {
                    $price = $price + (float) $variant->price_adjustment;
                }
            }

            DB::beginTransaction();

            try {
                // Lấy hoặc tạo cart
                $cart = $this->getOrCreateCart($request);

                // Kiểm tra sản phẩm đã có trong giỏ chưa (cùng product và variant)
                $existingItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->where('variant_id', $variantId)
                    ->first();

                if ($existingItem) {
                    // Cập nhật số lượng
                    $existingItem->quantity += $quantity;
                    $existingItem->save();
                } else {
                    // Tạo mới cart item
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'variant_id' => $variantId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'added_at' => now(),
                    ]);
                }

                DB::commit();

                // Đồng bộ với session để tương thích ngược
                $this->syncCartToSession($request, $cart);

                $cartCount = $cart->items()->count();

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                        'cart_count' => $cartCount,
                    ]);
                }

                return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . implode(', ', $e->errors()),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Cart add error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.',
                ], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        try {
            $cart = $this->getOrCreateCart($request);
            $items = $cart->items()->with('product')->get();
            $index = (int) $request->index;

            if (!isset($items[$index])) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
                }
                return back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
            }

            $item = $items[$index];
            $item->quantity = (int) $request->quantity;
            $item->save();

            // Đồng bộ với session
            $this->syncCartToSession($request, $cart);

            if ($request->ajax()) {
                $cartData = $this->prepareCart($request);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng!',
                    'cart' => $cartData,
                ]);
            }

            return back()->with('success', 'Đã cập nhật số lượng!');
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
        $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        try {
            $cart = $this->getOrCreateCart($request);
            $items = $cart->items()->with('product')->get();
            $index = (int) $request->index;

            if (!isset($items[$index])) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
                }
                return back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
            }

            $item = $items[$index];
            $item->delete();

            // Đồng bộ với session
            $this->syncCartToSession($request, $cart);

            if ($request->ajax()) {
                $cartData = $this->prepareCart($request);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                    'cart' => $cartData,
                ]);
            }

            return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
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

            // Xóa session cart
            $request->session()->forget('cart');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ giỏ hàng!',
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

    /**
     * Lấy hoặc tạo cart từ database
     */
    private function getOrCreateCart(Request $request): Cart
    {
        $user = $request->user();

        if ($user) {
            // User đã đăng nhập: lấy cart theo user_id
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['user_id' => $user->id]
            );
        } else {
            // User chưa đăng nhập: lấy cart từ session hoặc tạo mới
            $cartId = $request->session()->get('cart_id');
            
            if ($cartId) {
                $cart = Cart::find($cartId);
                if (!$cart) {
                    $cartId = null;
                }
            }

            if (!$cartId) {
                $cart = Cart::create(['user_id' => null]);
                $request->session()->put('cart_id', $cart->id);
            } else {
                $cart = Cart::find($cartId);
            }
        }

        return $cart;
    }

    /**
     * Đồng bộ cart từ database vào session để tương thích ngược
     */
    private function syncCartToSession(Request $request, Cart $cart): void
    {
        $items = $cart->items()->with(['product', 'variant.size', 'variant.scent', 'variant.concentration'])->get();
        
        $sessionItems = [];
        foreach ($items as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $variantName = '';
            if ($variant) {
                $parts = [];
                if ($variant->size) $parts[] = 'Size: ' . $variant->size->size_name;
                if ($variant->scent) $parts[] = 'Mùi: ' . $variant->scent->scent_name;
                if ($variant->concentration) $parts[] = 'Nồng độ: ' . $variant->concentration->concentration_name;
                $variantName = implode(' | ', $parts);
            }
            $sessionItems[] = [
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'name' => $product ? $product->name : 'Sản phẩm đã bị xóa',
                'variant_name' => $variantName,
                'image' => $product && $product->primaryImage() ? $product->primaryImage()->image_path : null,
            ];
        }

        $request->session()->put('cart', [
            'items' => $sessionItems,
            'shipping_fee' => 30000,
            'discount_total' => 0,
        ]);
    }

    /**
     * Chuẩn bị dữ liệu cart để hiển thị
     */
    private function prepareCart(Request $request): array
    {
        // Ưu tiên lấy từ database
        $cart = $this->getOrCreateCart($request);
        $items = $cart->items()->with('product')->get();

        $sessionItems = [];
        foreach ($items as $item) {
            $product = $item->product;
            if (!$product) {
                continue; // Bỏ qua sản phẩm đã bị xóa
            }

            $quantity = max(1, (int) $item->quantity);
            $price = (float) $item->price;

            $variant = $item->variant;
            $variantName = '';
            if ($variant) {
                $parts = [];
                if ($variant->size) $parts[] = 'Size: ' . $variant->size->size_name;
                if ($variant->scent) $parts[] = 'Mùi: ' . $variant->scent->scent_name;
                if ($variant->concentration) $parts[] = 'Nồng độ: ' . $variant->concentration->concentration_name;
                $variantName = implode(' | ', $parts);
            }
            $sessionItems[] = [
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $quantity * $price,
                'name' => $product->name,
                'variant_name' => $variantName,
                'image' => $product->primaryImage() ? $product->primaryImage()->image_path : null,
            ];
        }

        $subtotal = collect($sessionItems)->sum('subtotal');
        $shippingFee = 30000;
        $discountTotal = (float) ($request->session()->get('cart.discount_total', 0));
        $grandTotal = max(($subtotal + $shippingFee) - $discountTotal, 0);

        return [
            'items' => $sessionItems,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_total' => $discountTotal,
            'grand_total' => $grandTotal,
        ];
    }
}
