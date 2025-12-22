<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $selectedItems = array_filter(array_map('intval',
            is_string($request->input('selected_items'))
                ? explode(',', $request->input('selected_items'))
                : (array) $request->input('selected_items', [])
        ));

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $pending = $request->session()->get('pending_order', []);

        $defaultCustomer = [
            'customer_name'         => $pending['customer']['customer_name'] ?? optional($request->user())->name,
            'customer_email'        => $pending['customer']['customer_email'] ?? optional($request->user())->email,
            'customer_phone'        => $pending['customer']['customer_phone'] ?? optional($request->user())->phone,
            'shipping_address_line' => $pending['customer']['shipping_address_line'] ?? optional($request->user())->address,
            'customer_note'         => $pending['customer']['customer_note'] ?? null,
        ];

        $myVouchers = collect();
        if ($request->user()) {
            $voucherIds = $request->user()->userVouchers()->pluck('discount_id');
            if ($voucherIds->isNotEmpty()) {
                $myVouchers = Discount::valid()->whereIn('id', $voucherIds)->get();
            }
        }

        return view('client.checkout', compact('cart', 'selectedItems', 'defaultCustomer', 'myVouchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'         => 'required|string|max:150',
            'customer_email'        => 'nullable|email|max:150',
            'customer_phone'        => 'required|string|max:20',
            'shipping_address_line' => 'required|string|max:255',
            'customer_note'         => 'nullable|string|max:1000',
            'payment_method'        => 'required|in:cod,online',
        ]);

        $selectedItems = array_filter(array_map('intval',
            is_string($request->input('selected_items'))
                ? explode(',', $request->input('selected_items'))
                : (array) $request->input('selected_items', [])
        ));

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return back()->withErrors(['cart' => 'Giỏ hàng trống']);
        }

        // ===== ONLINE =====
        if ($validated['payment_method'] === 'online') {

            $vnpTxnRef = uniqid('vnp_');

            $request->session()->put('pending_order', [
                'customer'      => $validated,
                'cart'          => array_merge($cart, [
                    'discount_id' => $request->session()->get('cart.discount_id'),
                    'discount_code' => $request->session()->get('cart.code'),
                ]),
                'user_id'       => optional($request->user())->id,
                'selectedItems' => $selectedItems,
                'vnp_txn_ref'   => $vnpTxnRef,
            ]);

            return $this->redirectToVNPay($cart, $request, $vnpTxnRef);
        }

        // ===== COD =====
        $lastOrderId = null;
        DB::transaction(function () use ($validated, $cart, $request, $selectedItems, &$lastOrderId) {

            $order = Order::create([
                'user_id'               => optional($request->user())->id,
                'order_status'          => 'pending',
                'payment_method'        => 'cod',
                'subtotal'              => $cart['subtotal'],
                'shipping_cost'         => $cart['shipping_fee'],
                'discount_total'        => $cart['discount_total'],
                'discount_id'           => $request->session()->get('cart.discount_id') ?? null,
                'grand_total'           => $cart['grand_total'],
                'customer_name'         => $validated['customer_name'],
                'customer_email'        => $validated['customer_email'],
                'customer_phone'        => $validated['customer_phone'],
                'shipping_address_line' => $validated['shipping_address_line'],
                'customer_note'         => $validated['customer_note'] ?? null,
            ]);

            // tăng số lượt dùng mã (nếu có)
            if ($order->discount_id) {
                Discount::where('id', $order->discount_id)->increment('used_count');
            }

            $lastOrderId = $order->id;

            foreach ($cart['items'] as $item) {
                if (!in_array($item['cart_item_id'], $selectedItems)) continue;

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }

            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'cod',
                'amount'         => $order->grand_total,
                'status'         => 'pending',
            ]);

            // Xóa sản phẩm đã thanh toán trong giỏ
            $this->removePaidItemsFromCart($request, $selectedItems);
        });

        return redirect()->route('order.confirmation')
            ->with('success', 'Đặt hàng thành công')
            ->with('last_order_id', $lastOrderId);
    }

    private function redirectToVNPay(array $cart, Request $request, string $txnRef)
    {
        $vnp_TmnCode    = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url        = config('vnpay.vnp_Url');
        $vnp_ReturnUrl  = config('vnpay.vnp_ReturnUrl');

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => intval(round($cart['grand_total'] * 100)),
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $request->ip(),
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Thanh toan don hang",
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $txnRef,
        ];

        ksort($inputData);

        $query = [];
        foreach ($inputData as $k => $v) {
            $query[] = urlencode($k) . '=' . urlencode($v);
        }

        $hashData = implode('&', $query);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        return redirect()->away($vnp_Url . '?' . $hashData . '&vnp_SecureHash=' . $secureHash);
    }

    private function prepareCart(Request $request, array $selectedItems = []): array
    {
        $sessionCart = $request->session()->get('cart', ['items' => [], 'shipping_fee' => 30000, 'discount_total' => 0]);

        $items = collect($sessionCart['items'])->filter(
            fn($i) => empty($selectedItems) || in_array($i['cart_item_id'], $selectedItems)
        )->map(function ($i) {
            $i['quantity'] = max(1, (int)$i['quantity']);
            $i['subtotal'] = $i['quantity'] * $i['price'];
            return $i;
        });

        $subtotal = $items->sum('subtotal');
        
        // Only apply discount if a discount_id is set; otherwise reset discount_total to 0
        $discountTotal = 0;
        if ($request->session()->has('cart.discount_id') && $request->session()->get('cart.discount_id')) {
            $discountTotal = $sessionCart['discount_total'] ?? 0;
        }

        return [
            'items' => $items->values()->all(),
            'subtotal' => $subtotal,
            'shipping_fee' => $sessionCart['shipping_fee'],
            'discount_total' => $discountTotal,
            'grand_total' => max($subtotal + $sessionCart['shipping_fee'] - $discountTotal, 0),
        ];
    }

    public function confirmation(Request $request)
    {
        $orderId = session('last_order_id') ?? $request->session()->get('last_order_id');
        $order = null;
        if ($orderId) {
            $order = Order::with([
                'details.product',
                'details.variant.size',
                'details.variant.scent',
                'details.variant.concentration'
            ])->find($orderId);
        }

        return view('client.order-confirmation', compact('order'));
    }

    private function removePaidItemsFromCart(Request $request, array $paidItemIds): void
    {
        $sessionCart = $request->session()->get('cart', ['items' => []]);
        $remainingItems = collect($sessionCart['items'])
            ->reject(fn($i) => in_array($i['cart_item_id'], $paidItemIds))
            ->values()
            ->all();

        $request->session()->put('cart', array_merge($sessionCart, ['items' => $remainingItems]));

        $cart = Cart::find($request->session()->get('cart_id'));
        if ($cart) {
            $cart->items()->whereIn('id', $paidItemIds)->delete();
        }
    }
}
