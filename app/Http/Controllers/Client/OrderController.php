<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with(['details.product']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $email = $request->input('email') ?? $request->session()->get('last_order_email');
            $phone = $request->input('phone') ?? $request->session()->get('last_order_phone');

            if ($email) $query->where('customer_email', $email);
            elseif ($phone) $query->where('customer_phone', $phone);
            else $orders = collect();
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    // Chi tiết đơn hàng
    public function show(Request $request, $id)
    {
        $query = Order::with(['details.product']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $email = $request->session()->get('last_order_email');
            $phone = $request->session()->get('last_order_phone');

            if ($email) $query->where('customer_email', $email);
            elseif ($phone) $query->where('customer_phone', $phone);
            else abort(404);
        }

        $order = $query->findOrFail($id);

        return view('client.orders.show', compact('order'));
    }

    // Cập nhật thông tin giao hàng
    public function updateShipping(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->order_status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn hàng không thể cập nhật.');
        }

        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'nullable|email|max:255',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'customer_note'    => 'nullable|string|max:1000',
        ]);

        $order->update($request->only([
            'customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'customer_note',
        ]));

        return redirect()->back()->with('success', 'Cập nhật thông tin giao hàng thành công.');
    }

    // Hủy đơn hàng
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->order_status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Đơn hàng không thể hủy.');
        }

        $order->update([
            'order_status' => 'canceled',
        ]);

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }
}
