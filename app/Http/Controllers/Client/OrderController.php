<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Helpers\OrderStatusHelper;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with(['details.product', 'details.variant.size', 'details.variant.scent', 'details.variant.concentration']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $email = $request->input('email') ?? $request->session()->get('last_order_email');
            $phone = $request->input('phone') ?? $request->session()->get('last_order_phone');

            if ($email) {
                $query->where('customer_email', $email);
            } elseif ($phone) {
                $query->where('customer_phone', $phone);
            } else {
                // Nếu không có email/phone và không đăng nhập, trả về collection rỗng
                $orders = \Illuminate\Pagination\LengthAwarePaginator::make([], 0, 10);
                return view('client.orders.index', compact('orders'));
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    // Chi tiết đơn hàng
    public function show(Request $request, $id)
    {
        $query = Order::with(['details.product', 'details.variant.size', 'details.variant.scent', 'details.variant.concentration']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $email = $request->input('email') ?? $request->session()->get('last_order_email');
            $phone = $request->input('phone') ?? $request->session()->get('last_order_phone');

            if ($email) {
                $query->where('customer_email', $email);
            } elseif ($phone) {
                $query->where('customer_phone', $phone);
            } else {
                abort(404, 'Không tìm thấy đơn hàng. Vui lòng đăng nhập hoặc nhập email/số điện thoại.');
            }
        }

        $order = $query->with('payment')->findOrFail($id);
        
        // Kiểm tra xem đơn hàng đã thanh toán chưa
        $isPaid = ($order->payment && $order->payment->status === 'paid') || $order->payment_method !== null;
        $mappedStatus = OrderStatusHelper::mapOldStatus($order->order_status);
        $canUpdateShipping = $mappedStatus === OrderStatusHelper::PENDING && !$isPaid;

        return view('client.orders.show', compact('order', 'isPaid', 'canUpdateShipping'));
    }

    // Cập nhật thông tin giao hàng
    public function updateShipping(Request $request, $id)
    {
        $order = Order::with('payment')->findOrFail($id);

        // Kiểm tra đơn hàng đã thanh toán chưa
        $isPaid = ($order->payment && $order->payment->status === 'paid') || $order->payment_method !== null;
        if ($isPaid) {
            return redirect()->back()->with('error', 'Đơn hàng đã thanh toán, không thể cập nhật thông tin giao hàng.');
        }

        $mappedStatus = OrderStatusHelper::mapOldStatus($order->order_status);
        if ($mappedStatus !== OrderStatusHelper::PENDING) {
            return redirect()->back()->with('error', 'Đơn hàng không thể cập nhật.');
        }

        $user = $request->user();
        
        if ($user) {
            // Nếu đã đăng nhập, lấy tên và email từ tài khoản
            $validated = $request->validate([
                'customer_phone'        => 'required|string|max:20',
                'shipping_address_line' => 'required|string|max:500',
                'customer_note'         => 'nullable|string|max:1000',
            ]);
            
            $validated['customer_name'] = $user->name;
            $validated['customer_email'] = $user->email;
        } else {
            $validated = $request->validate([
                'customer_name'         => 'required|string|max:255',
                'customer_email'        => 'nullable|email|max:255',
                'customer_phone'        => 'required|string|max:20',
                'shipping_address_line' => 'required|string|max:500',
                'customer_note'         => 'nullable|string|max:1000',
            ]);
        }

        $order->update($validated);

        return redirect()->back()->with('success', 'Cập nhật thông tin giao hàng thành công.');
    }

    // Hủy đơn hàng
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($request->user() && $order->user_id != $request->user()->id) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Map trạng thái cũ sang mới để check
        $mappedStatus = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
        
        // Chỉ cho hủy ở trạng thái PENDING hoặc PREPARING
        if (!in_array($mappedStatus, [\App\Helpers\OrderStatusHelper::PENDING, \App\Helpers\OrderStatusHelper::PREPARING])) {
            return redirect()->back()->with('error', 'Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        $order->update([
            'order_status' => \App\Helpers\OrderStatusHelper::CANCELLED,
            'cancelled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }
    
    //Xác nhận nhận hàng
    public function confirmReceived(Request $request, $id)
{
    $order = Order::where('user_id', $request->user()->id)
                  ->findOrFail($id);

        // Map trạng thái để check
        $mappedStatus = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
        
        // Chỉ cho xác nhận khi trạng thái là DELIVERED
        if ($mappedStatus !== \App\Helpers\OrderStatusHelper::DELIVERED) {
            return redirect()->back()->with('error', 'Chỉ có thể xác nhận đơn hàng đã được giao.');
        }

    $order->update([
            'order_status' => \App\Helpers\OrderStatusHelper::COMPLETED,
        'completed_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Cảm ơn bạn! Đơn hàng đã được xác nhận.');
}

}
