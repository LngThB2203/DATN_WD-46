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

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->prepareCart($request);

        $defaultCustomer = [
            'name' => optional($request->user())->name,
            'email' => optional($request->user())->email,
            'phone' => optional($request->user())->phone ?? null,
        ];

        return view('client.checkout', [
            'cart' => $cart,
            'defaultCustomer' => $defaultCustomer,
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

        $cart = $this->prepareCart($request);

        if (empty($cart['items'])) {
            return back()
                ->withInput()
                ->withErrors(['cart' => 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi thanh toán.']);
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

            $request->session()->forget('cart');

            $order->load(['details.product']);

            if ($order->customer_email) {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            }

            $orderCode = '#' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
            $successMessage = $validated['payment_method'] === 'bank_transfer'
                ? "Đơn hàng {$orderCode} đã được ghi nhận. Vui lòng chuyển khoản theo hướng dẫn để hoàn tất thanh toán."
                : "Đơn hàng {$orderCode} đã được ghi nhận. Chúng tôi sẽ liên hệ sớm nhất.";

            return redirect()
                ->route('checkout.index')
                ->with('success', $successMessage);
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi lưu đơn hàng. Vui lòng thử lại sau.');
        }
    }

    private function prepareCart(Request $request): array
    {
        $rawCart = $request->session()->get('cart', [
            'items' => [],
            'shipping_fee' => 0,
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

