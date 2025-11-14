<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index()
    {
        $orders = Order::with('user')->orderBy('id', 'DESC')->paginate(15);

        return view('admin.orders.list', compact('orders'));
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'user',
            'discount',
            'payment',
            'shipment',
            'details.product',
            'details.variant.size',
            'details.variant.scent',
            'details.variant.concentration'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required'
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'order_status' => $request->order_status
        ]);

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    // Cập nhật trạng thái giao hàng
    public function updateShipment(Request $request, $id)
    {
        $request->validate([
            'shipping_status' => 'required'
        ]);

        $order = Order::findOrFail($id);

        Shipment::updateOrCreate(
            ['order_id' => $id],
            [
                'shipping_status' => $request->shipping_status,
                'tracking_number' => $request->tracking_number,
                'carrier' => $request->carrier,
            ]
        );

        return back()->with('success', 'Cập nhật trạng thái giao hàng thành công!');
    }
}
