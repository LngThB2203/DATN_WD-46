<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách các sản phẩm được chọn
        $selectedItems = $request->input('selected_items');
        if (is_string($selectedItems)) {
            $selectedItems = explode(',', $selectedItems);
        }
        $selectedItems = $selectedItems ? array_filter(array_map('intval', (array) $selectedItems)) : null;

        $cart = $this->prepareCart($request, $selectedItems);

        // Nếu không có sản phẩm nào được chọn, redirect về giỏ hàng
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $defaultCustomer = [
            'name' => optional($request->user())->name,
            'email' => optional($request->user())->email,
            'phone' => optional($request->user())->phone ?? null,
        ];

        return view('client.checkout', [
            'cart' => $cart,
            'defaultCustomer' => $defaultCustomer,
            'selectedItems' => $selectedItems,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_province' => ['required', 'string', 'max:120'],
            'shipping_district' => ['required', 'string', 'max:120'],
            'shipping_ward' => ['nullable', 'string', 'max:120'],
            'shipping_address_line' => ['required', 'string', 'max:255'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(['cod', 'bank_transfer'])],
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_province.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'shipping_district.required' => 'Vui lòng chọn quận/huyện.',
            'shipping_address_line.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        // Lấy danh sách các sản phẩm được chọn
        $selectedItems = $request->input('selected_items');
        if (is_string($selectedItems)) {
            $selectedItems = explode(',', $selectedItems);
        }
        $selectedItems = $selectedItems ? array_filter(array_map('intval', (array) $selectedItems)) : null;

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return back()
                ->withInput()
                ->withErrors(['cart' => 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.']);
        }

        DB::beginTransaction();

        try {
            $fullAddress = $this->buildFullAddress($validated);
            $discountId = $request->session()->get('cart.discount_id');

            $order = Order::create([
                'user_id' => optional($request->user())->id,
                'discount_id' => $discountId,
                'order_status' => 'pending',
                'total_price' => $cart['subtotal'],
                'shipping_address' => $fullAddress,
                'shipping_cost' => $cart['shipping_fee'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_district' => $validated['shipping_district'],
                'shipping_ward' => $validated['shipping_ward'] ?? null,
                'shipping_address_line' => $validated['shipping_address_line'],
                'customer_note' => $validated['customer_note'] ?? null,
                'subtotal' => $cart['subtotal'],
                'discount_total' => $cart['discount_total'],
                'grand_total' => $cart['grand_total'],
                'payment_method' => $validated['payment_method'],
            ]);

            $orderDetailsPayload = collect($cart['items'])->map(function (array $item) use ($order) {
                $quantity = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['price'] ?? 0);

                return [
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $quantity * $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            if ($orderDetailsPayload->isNotEmpty()) {
                OrderDetail::insert($orderDetailsPayload->all());
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'transaction_code' => null,
                'amount' => $cart['grand_total'],
                'status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'paid_at' => null,
            ]);

            $orderStatus = $validated['payment_method'] === 'cod' ? 'processing' : 'awaiting_payment';
            $order->update(['order_status' => $orderStatus]);

            if ($discountId) {
                $discount = Discount::find($discountId);
                if ($discount) {
                    $discount->incrementUsage();
                }
            }

            DB::commit();

            // Chỉ xóa các sản phẩm đã thanh toán khỏi giỏ hàng, không xóa toàn bộ
            if ($selectedItems !== null && !empty($selectedItems)) {
                $cart = $request->session()->get('cart', ['items' => []]);
                $remainingItems = [];
                foreach ($cart['items'] ?? [] as $index => $item) {
                    if (!in_array($index, $selectedItems, true)) {
                        $remainingItems[] = $item;
                    }
                }
                $cart['items'] = $remainingItems;
                // Reset lại subtotal và grand_total
                $cart['subtotal'] = 0;
                $cart['grand_total'] = 0;
                $cart['discount_total'] = 0;
                $request->session()->put('cart', $cart);
            } else {
                // Nếu không có selected_items, xóa toàn bộ giỏ hàng (fallback)
                $request->session()->forget('cart');
            }

            $order->load(['details.product']);

            if ($order->customer_email) {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            }

            // Lưu thông tin customer vào session để có thể xem đơn hàng sau
            if ($validated['customer_email']) {
                $request->session()->put('last_order_email', $validated['customer_email']);
            }
            if ($validated['customer_phone']) {
                $request->session()->put('last_order_phone', $validated['customer_phone']);
            }

            $orderCode = '#' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
            $successMessage = $validated['payment_method'] === 'bank_transfer'
                ? "Đơn hàng {$orderCode} đã được ghi nhận. Vui lòng chuyển khoản theo hướng dẫn để hoàn tất thanh toán."
                : "Đơn hàng {$orderCode} đã được ghi nhận. Chúng tôi sẽ liên hệ sớm nhất.";

            return redirect()
                ->route('orders.index')
                ->with('success', $successMessage);
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            Log::error('Checkout error: ' . $exception->getMessage(), [
                'trace' => $exception->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi lưu đơn hàng: ' . $exception->getMessage() . '. Vui lòng thử lại sau.');
        }
    }

    private function prepareCart(Request $request, ?array $selectedItems = null): array
    {
        $rawCart = $request->session()->get('cart', [
            'items' => [],
            'shipping_fee' => 0,
            'discount_total' => 0,
        ]);

        $allItems = collect($rawCart['items'] ?? []);

        // Nếu có selected_items, chỉ lấy các sản phẩm được chọn
        if ($selectedItems !== null && !empty($selectedItems)) {
            $allItems = $allItems->filter(function ($item, $index) use ($selectedItems) {
                return in_array($index, $selectedItems, true);
            });
        }

        $items = $allItems->map(function ($item) {
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $price = (float) ($item['price'] ?? 0);
            $item['quantity'] = $quantity;
            $item['price'] = $price;
            $item['subtotal'] = $quantity * $price;

            return $item;
        });

        $subtotal = $items->sum('subtotal');
        $shippingFee = (float) ($rawCart['shipping_fee'] ?? 0);
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

    private function buildFullAddress(array $data): string
    {
        return collect([
            $data['shipping_address_line'] ?? null,
            $data['shipping_ward'] ?? null,
            $data['shipping_district'] ?? null,
            $data['shipping_province'] ?? null,
        ])
            ->filter()
            ->implode(', ');
    }
}

