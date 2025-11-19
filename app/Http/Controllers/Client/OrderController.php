<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Nếu user đã đăng nhập, lấy đơn hàng theo user_id
        // Nếu chưa đăng nhập, lấy đơn hàng theo email hoặc phone từ session
        $query = Order::with(['details.product', 'payment']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            // Lấy đơn hàng theo email hoặc phone từ session (nếu có)
            $customerEmail = $request->session()->get('last_order_email');
            $customerPhone = $request->session()->get('last_order_phone');
            
            if ($customerEmail) {
                $query->where('customer_email', $customerEmail);
            } elseif ($customerPhone) {
                $query->where('customer_phone', $customerPhone);
            } else {
                // Nếu không có thông tin, trả về empty
                $orders = collect();
                return view('client.orders.index', compact('orders'));
            }
        }

        $orders = $query->orderBy('created_at', 'DESC')->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function show(Request $request, $id)
    {
        $query = Order::with(['details.product', 'payment', 'discount']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            // Kiểm tra theo email hoặc phone
            $customerEmail = $request->session()->get('last_order_email');
            $customerPhone = $request->session()->get('last_order_phone');
            
            if ($customerEmail) {
                $query->where('customer_email', $customerEmail);
            } elseif ($customerPhone) {
                $query->where('customer_phone', $customerPhone);
            } else {
                abort(404);
            }
        }

        $order = $query->findOrFail($id);

        return view('client.orders.show', compact('order'));
    }
}

