<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VNPayController extends Controller
{
    public function vnpayReturn(Request $request)
    {
        $secret = config('vnpay.vnp_HashSecret');
        $data   = $request->all();

        $secureHash = $data['vnp_SecureHash'] ?? null;
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);
        ksort($data);

        $hashData = '';
        foreach ($data as $k => $v) {
            if ($hashData !== '') $hashData .= '&';
            $hashData .= urlencode($k) . '=' . urlencode($v);
        }

        // Kiểm tra chữ ký
        if (hash_hmac('sha512', $hashData, $secret) !== $secureHash) {
            return redirect()->route('checkout.index')
                ->with('error', 'Sai chữ ký VNPay');
        }

        // Kiểm tra thanh toán thành công
        if ($request->vnp_ResponseCode !== '00') {
            return redirect()->route('checkout.index')
                ->with('error', 'Thanh toán không thành công');
        }

        $pending = session('pending_order');
        if (!$pending) {
            return redirect()->route('cart.index')
                ->with('error', 'Phiên thanh toán đã hết hạn');
        }

        DB::transaction(function () use ($pending, $request) {
            $order = Order::create([
                'user_id'               => $pending['user_id'],
                'order_status'          => 'pending',
                'payment_method'        => 'online',
                'subtotal'              => $pending['cart']['subtotal'],
                'shipping_cost'         => $pending['cart']['shipping_fee'],
                'discount_total'        => $pending['cart']['discount_total'],
                'grand_total'           => $pending['cart']['grand_total'],
                'customer_name'         => $pending['customer']['customer_name'],
                'customer_email'        => $pending['customer']['customer_email'],
                'customer_phone'        => $pending['customer']['customer_phone'],
                'shipping_address_line' => $pending['customer']['shipping_address_line'],
                'customer_note'         => $pending['customer']['customer_note'] ?? null,
            ]);

            foreach ($pending['cart']['items'] as $item) {
                if (!in_array($item['cart_item_id'], $pending['selectedItems'])) continue;

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
                'order_id'         => $order->id,
                'payment_method'   => 'online',
                'transaction_code' => $request->vnp_TransactionNo,
                'amount'           => $order->grand_total,
                'status'           => 'paid',
                'paid_at'          => now(),
            ]);

            // Xóa sản phẩm đã thanh toán khỏi giỏ
            $cartId = session('cart_id');
            if ($cartId) {
                $cart = \App\Models\Cart::find($cartId);
                if ($cart) {
                    $cart->items()->whereIn('id', $pending['selectedItems'])->delete();
                }
            }
            session()->forget('cart');
        });

        session()->forget('pending_order');

        return redirect()->route('orders.index')
            ->with('success', 'Thanh toán thành công');
    }
}
