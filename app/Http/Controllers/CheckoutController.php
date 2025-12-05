<?php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);
        if (is_string($selectedItems)) {
            $selectedItems = explode(',', $selectedItems);
        }
        $selectedItems = array_filter(array_map('intval', (array) $selectedItems));

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $defaultCustomer = [
            'name'    => optional($request->user())->name,
            'email'   => optional($request->user())->email,
            'phone'   => optional($request->user())->phone ?? null,
            'address' => optional($request->user())->address ?? null,
        ];

        return view('client.checkout', [
            'cart'            => $cart,
            'defaultCustomer' => $defaultCustomer,
            'selectedItems'   => $selectedItems,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'         => 'required|string|max:150',
            'customer_email'        => 'nullable|email|max:150',
            'customer_phone'        => 'required|string|max:20',
            'shipping_address_line' => 'required|string|max:255',
            'customer_note'         => 'nullable|string|max:1000',
            'payment_method'        => 'required|string|in:cod,bank_transfer,online',
        ]);

        $selectedItems = $request->input('selected_items', []);
        if (is_string($selectedItems)) {
            $selectedItems = explode(',', $selectedItems);
        }
        $selectedItems = array_filter(array_map('intval', (array) $selectedItems));

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return back()->withInput()->withErrors(['cart' => 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.']);
        }

        DB::beginTransaction();
        try {
            $fullAddress = $validated['shipping_address_line'];
            $discountId  = $request->session()->get('cart.discount_id');

            // Tạo đơn hàng
            $order = Order::create([
                'user_id'               => optional($request->user())->id,
                'discount_id'           => $discountId,
                'order_status'          => 'pending',
                'total_price'           => $cart['subtotal'],
                'shipping_address'      => $fullAddress,
                'shipping_cost'         => $cart['shipping_fee'],
                'customer_name'         => $validated['customer_name'],
                'customer_email'        => $validated['customer_email'] ?? null,
                'customer_phone'        => $validated['customer_phone'],
                'shipping_address_line' => $validated['shipping_address_line'],
                'customer_note'         => $validated['customer_note'] ?? null,
                'subtotal'              => $cart['subtotal'],
                'discount_total'        => $cart['discount_total'],
                'grand_total'           => $cart['grand_total'],
                'payment_method'        => $validated['payment_method'],
            ]);

            // Tạo order details
            $orderDetails = [];
            foreach ($cart['items'] as $item) {
                $orderDetails[] = [
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (! empty($orderDetails)) {
                OrderDetail::insert($orderDetails);
            }

            // Tạo payment
            Payment::create([
                'order_id'         => $order->id,
                'payment_method'   => $validated['payment_method'],
                'transaction_code' => null,
                'amount'           => $cart['grand_total'],
                'status'           => 'pending',
                'paid_at'          => null,
            ]);

            // Discount
            if ($discountId && $discount = Discount::find($discountId)) {
                $discount->incrementUsage();
            }

            DB::commit();

            // Xóa các item đã thanh toán khỏi cart
            $this->removePaidItemsFromCart($request, $selectedItems);

            // ---------- VNPay ----------
            if ($validated['payment_method'] === 'online') {
                // dùng config() thay vì env()
                $vnp_TmnCode    = config('vnpay.vnp_TmnCode');
                $vnp_HashSecret = config('vnpay.vnp_HashSecret');
                $vnp_Url        = config('vnpay.vnp_Url');
                $vnp_ReturnUrl  = config('vnpay.vnp_ReturnUrl');
                $vnp_IpnUrl     = config('vnpay.vnp_IpnUrl');

                $vnp_TxnRef    = (string) $order->id; // giữ order id để dễ xử lý return/ipn
                $vnp_OrderInfo = "Thanh toan don hang {$order->id}";
                $vnp_OrderType = "billpayment";
                // ép kiểu integer (bắt buộc)
                $vnp_Amount     = intval(round($order->grand_total * 100));
                $vnp_Locale     = "vn";
                $vnp_IpAddr     = $request->ip();
                $vnp_CreateDate = date('YmdHis');

                $inputData = [
                    "vnp_Version"    => "2.1.0",
                    "vnp_TmnCode"    => $vnp_TmnCode,
                    "vnp_Amount"     => $vnp_Amount,
                    "vnp_Command"    => "pay",
                    "vnp_CreateDate" => $vnp_CreateDate,
                    "vnp_CurrCode"   => "VND",
                    "vnp_IpAddr"     => $vnp_IpAddr,
                    "vnp_Locale"     => $vnp_Locale,
                    "vnp_OrderInfo"  => $vnp_OrderInfo,
                    "vnp_OrderType"  => $vnp_OrderType,
                    "vnp_ReturnUrl"  => $vnp_ReturnUrl,
                    "vnp_TxnRef"     => $vnp_TxnRef,
                ];

                ksort($inputData);

                $query       = [];
                $hashDataArr = [];

                foreach ($inputData as $key => $value) {
                    $query[]       = urlencode($key) . "=" . urlencode($value);
                    $hashDataArr[] = urlencode($key) . "=" . urlencode($value);
                }

                $queryString = implode("&", $query);
                $hashData    = implode("&", $hashDataArr);

                $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

                $requestUrl = $vnp_Url . "?" . $queryString . "&vnp_SecureHash=" . $vnpSecureHash;

                // Log để debug nếu redirect không chạy
                Log::info('VNPay redirect url: ' . $requestUrl, [
                    'order_id' => $order->id,
                    'amount'   => $vnp_Amount,
                ]);

                return redirect()->away($requestUrl);
            }
            // ---------- end VNPay ----------

            return redirect()->route('orders.index')
                ->with('success', "Đơn hàng #{$order->id} đã được ghi nhận.");

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Checkout error: ' . $exception->getMessage(), [
                'trace'   => $exception->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi lưu đơn hàng. Vui lòng thử lại.');
        }
    }

    private function prepareCart(Request $request, array $selectedItems = []): array
    {
        $sessionCart = $request->session()->get('cart', ['items' => [], 'shipping_fee' => 30000, 'discount_total' => 0]);
        $items       = collect($sessionCart['items'] ?? []);

        if (! empty($selectedItems)) {
            $items = $items->filter(fn($i) => in_array($i['cart_item_id'], $selectedItems, true));
        }

        $items = $items->map(function ($i) {
            $i['quantity'] = max(1, (int) ($i['quantity'] ?? 1));
            $i['price']    = (float) ($i['price'] ?? 0);
            $i['subtotal'] = $i['quantity'] * $i['price'];
            return $i;
        });

        $subtotal      = $items->sum('subtotal');
        $shippingFee   = (float) ($sessionCart['shipping_fee'] ?? 0);
        $discountTotal = (float) ($sessionCart['discount_total'] ?? 0);
        $grandTotal    = max(($subtotal + $shippingFee) - $discountTotal, 0);

        return [
            'items'          => $items->all(),
            'subtotal'       => $subtotal,
            'shipping_fee'   => $shippingFee,
            'discount_total' => $discountTotal,
            'grand_total'    => $grandTotal,
        ];
    }

    private function removePaidItemsFromCart(Request $request, array $paidItemIds): void
    {
        $sessionCart = $request->session()->get('cart', ['items' => []]);

        $sessionCart['items'] = collect($sessionCart['items'])
            ->reject(fn($i) => in_array($i['cart_item_id'], $paidItemIds, true))
            ->values()
            ->all();

        $subtotal = collect($sessionCart['items'])
            ->sum(fn($i) => $i['quantity'] * $i['price']);

        $sessionCart['subtotal']    = $subtotal;
        $sessionCart['grand_total'] = max(($subtotal + ($sessionCart['shipping_fee'] ?? 0)) - ($sessionCart['discount_total'] ?? 0), 0);

        $request->session()->put('cart', $sessionCart);

        if ($request->user()) {
            CartItem::whereIn('id', $paidItemIds)->delete();
        }
    }
}
