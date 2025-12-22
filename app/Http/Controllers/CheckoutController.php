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
         if (Auth::check() && Auth::user()->status == 0) {
        return redirect()->route('cart.index')
            ->with('error', 'Tài khoản của bạn đang bị khóa, không thể thanh toán đơn hàng.Vui lòng liên hệ quản trị viên.');
    }
        $selectedItems = array_filter(array_map('intval',
            is_string($request->input('selected_items'))
                ? explode(',', $request->input('selected_items'))
                : (array) $request->input('selected_items', [])
        ));

        //
        $cart = $this->prepareCart($request, $selectedItems, true);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $pending = $request->session()->get('pending_order', []);
        $user = $request->user();

        // Nếu đã đăng nhập, bắt buộc lấy tên và email từ tài khoản
        $defaultCustomer = [
            'customer_name'         => $user ? $user->name : ($pending['customer']['customer_name'] ?? ''),
            'customer_email'        => $user ? $user->email : ($pending['customer']['customer_email'] ?? ''),
            'customer_phone'        => $pending['customer']['customer_phone'] ?? optional($user)->phone,
            'shipping_address_line' => $pending['customer']['shipping_address_line'] ?? optional($user)->address,
            'customer_note'         => $pending['customer']['customer_note'] ?? null,
        ];

        $myVouchers = collect();
        if ($request->user()) {
            $voucherIds = $request->user()->userVouchers()->pluck('discount_id');

            if ($voucherIds->isNotEmpty()) {
                $usedDiscountIds = Order::where('user_id', $request->user()->id)
                    ->whereNotNull('discount_id')
                    ->where(function ($q) {
                        $q->whereNull('order_status')
                          ->orWhere('order_status', '!=', 'cancelled');
                    })
                    ->pluck('discount_id');

                $myVouchers = Discount::valid()
                    ->whereIn('id', $voucherIds)
                    ->when($usedDiscountIds->isNotEmpty(), function ($q) use ($usedDiscountIds) {
                        $q->whereNotIn('id', $usedDiscountIds);
                    })
                    ->get();
            }
        }

        $isLoggedIn = $user !== null;

        return view('client.checkout', compact('cart', 'selectedItems', 'defaultCustomer', 'myVouchers', 'isLoggedIn'));
    }

    public function store(Request $request)
    {
          if (!Auth::check()) {
        return redirect()->route('login')
            ->with('error', 'Vui lòng đăng nhập trước khi đặt hàng');
    }

    //CHẶN USER BỊ KHÓA
    if (Auth::user()->status == 0) {
        return redirect()->route('checkout.index')
            ->with('error', 'Tài khoản của bạn đang bị khóa. Vui lòng liên hệ quản trị viên.');
    }
         $user = $request->user();

        // Nếu đã đăng nhập, bắt buộc lấy tên và email từ tài khoản
        if ($user) {
            $validated = $request->validate([
                'customer_phone'        => 'required|string|max:20',
                'shipping_address_line' => 'required|string|max:255',
                'customer_note'         => 'nullable|string|max:1000',
                'payment_method'        => 'required|in:cod,online',
            ]);

            // Lấy tên và email từ tài khoản
            $validated['customer_name'] = $user->name;
            $validated['customer_email'] = $user->email;
        } else {
            $validated = $request->validate([
                'customer_name'         => 'required|string|max:150',
                'customer_email'        => 'required|email|max:150',
                'customer_phone'        => 'required|string|max:20',
                'shipping_address_line' => 'required|string|max:255',
                'customer_note'         => 'nullable|string|max:1000',
                'payment_method'        => 'required|in:cod,online',
            ]);
        }

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
                'cart'          => $cart,
                'user_id'       => optional($request->user())->id,
                'selectedItems' => $selectedItems,
                'vnp_txn_ref'   => $vnpTxnRef,
            ]);

            return $this->redirectToVNPay($cart, $request, $vnpTxnRef);
        }

        // ===== COD =====
        DB::transaction(function () use ($validated, $cart, $request, $selectedItems) {

            $order = Order::create([
                'user_id'               => optional($request->user())->id,
                'discount_id'           => $cart['discount_id'] ?? null,
                'order_status'          => 'pending',
                'payment_method'        => 'cod',
                'subtotal'              => $cart['subtotal'],
                'shipping_cost'         => $cart['shipping_fee'],
                'discount_total'        => $cart['discount_total'],
                'grand_total'           => $cart['grand_total'],
                'customer_name'         => $validated['customer_name'],
                'customer_email'        => $validated['customer_email'],
                'customer_phone'        => $validated['customer_phone'],
                'shipping_address_line' => $validated['shipping_address_line'],
                'customer_note'         => $validated['customer_note'] ?? null,
            ]);

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

            // Nếu đơn hàng có áp mã giảm giá, tăng số lượt đã dùng cho mã đó
            if ($order->discount_id) {
                $order->discount?->incrementUsage();
            }

            // Xóa sản phẩm đã thanh toán trong giỏ
            $this->removePaidItemsFromCart($request, $selectedItems);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Đặt hàng thành công');
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

    private function prepareCart(Request $request, array $selectedItems = [], bool $resetDiscount = false): array
    {
        $sessionCart = $request->session()->get('cart', [
            'items'          => [],
            'shipping_fee'   => 30000,
            'discount_total' => 0,
        ]);

        // Chỉ reset discount khi được yêu cầu (mở checkout lần đầu từ giỏ),
        // không reset trong bước submit đơn hàng để giữ nguyên giảm giá đã áp dụng.
        if ($resetDiscount && !empty($selectedItems)) {
            $sessionCart['discount_id']    = null;
            $sessionCart['discount_total'] = 0;
            $sessionCart['discount_code']  = null;
        }

        $items = collect($sessionCart['items'])->filter(
            fn($i) => empty($selectedItems) || in_array($i['cart_item_id'], $selectedItems)
        )->map(function ($i) {
            $i['quantity'] = max(1, (int)$i['quantity']);
            $i['subtotal'] = $i['quantity'] * $i['price'];
            return $i;
        });

        $subtotal = $items->sum('subtotal');

        return [
            'items'          => $items->values()->all(),
            'subtotal'       => $subtotal,
            'shipping_fee'   => $sessionCart['shipping_fee'],
            'discount_total' => $sessionCart['discount_total'],
            'grand_total'    => max($subtotal + $sessionCart['shipping_fee'] - $sessionCart['discount_total'], 0),
            'discount_id'    => $sessionCart['discount_id'] ?? null,
        ];
    }

    private function removePaidItemsFromCart(Request $request, array $paidItemIds): void
    {
        $sessionCart = $request->session()->get('cart', ['items' => []]);
        $remainingItems = collect($sessionCart['items'])
            ->reject(fn($i) => in_array($i['cart_item_id'], $paidItemIds))
            ->values()
            ->all();

        // Sau khi thanh toán xong, xóa thông tin giảm giá khỏi giỏ
        $clearedCart = array_merge($sessionCart, [
            'items'          => $remainingItems,
            'discount_id'    => null,
            'discount_total' => 0,
            'discount_code'  => null,
        ]);

        $request->session()->put('cart', $clearedCart);

        $cart = Cart::find($request->session()->get('cart_id'));
        if ($cart) {
            $cart->items()->whereIn('id', $paidItemIds)->delete();
        }
    }
}
