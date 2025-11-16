<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $price = $product->sale_price ?? $product->price;

            $cart = $request->session()->get('cart', [
                'items' => [],
                'shipping_fee' => 30000,
                'discount_total' => 0,
            ]);

            // Kiểm tra sản phẩm đã có trong giỏ chưa
            $existingIndex = collect($cart['items'])->search(function ($item) use ($request) {
                return ($item['product_id'] ?? null) == $request->product_id;
            });

            if ($existingIndex !== false) {
                $cart['items'][$existingIndex]['quantity'] += $quantity;
            } else {
                $cart['items'][] = [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'name' => $product->name,
                    'image' => $product->primaryImage() ? $product->primaryImage()->image_path : null,
                ];
            }

            $request->session()->put('cart', $cart);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                    'cart_count' => count($cart['items']),
                ]);
            }

            return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
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

        $cart = $request->session()->get('cart', ['items' => []]);
        $index = (int) $request->index;

        if (!isset($cart['items'][$index])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
            }
            return back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
        }

        $cart['items'][$index]['quantity'] = (int) $request->quantity;
        $request->session()->put('cart', $cart);

        if ($request->ajax()) {
            $cart = $this->prepareCart($request);
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng!',
                'cart' => $cart,
            ]);
        }

        return back()->with('success', 'Đã cập nhật số lượng!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $cart = $request->session()->get('cart', ['items' => []]);
        $index = (int) $request->index;

        if (!isset($cart['items'][$index])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
            }
            return back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
        }

        unset($cart['items'][$index]);
        $cart['items'] = array_values($cart['items']);
        $request->session()->put('cart', $cart);

        if ($request->ajax()) {
            $cart = $this->prepareCart($request);
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'cart' => $cart,
            ]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng!',
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }

    private function prepareCart(Request $request): array
    {
        $rawCart = $request->session()->get('cart', [
            'items' => [],
            'shipping_fee' => 30000,
            'discount_total' => 0,
        ]);

        $items = collect($rawCart['items'] ?? [])->map(function ($item) {
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $price = (float) ($item['price'] ?? 0);
            $item['quantity'] = $quantity;
            $item['price'] = $price;
            $item['subtotal'] = $quantity * $price;

            return $item;
        });

        $subtotal = $items->sum('subtotal');
        $shippingFee = (float) ($rawCart['shipping_fee'] ?? 30000);
        $discountTotal = (float) ($rawCart['discount_total'] ?? 0);
        $grandTotal = max(($subtotal + $shippingFee) - $discountTotal, 0);

        return [
            'items' => $items->all(),
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_total' => $discountTotal,
            'grand_total' => $grandTotal,
        ];
    }
}

