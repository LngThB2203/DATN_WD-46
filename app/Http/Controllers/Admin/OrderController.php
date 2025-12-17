<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\OrderStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index()
    {
        $orders = Order::latest()->paginate(20);
        return view('admin.orders.list', compact('orders'));
    }

    // Chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'details.product',
            'details.variant.size',
            'details.variant.scent',
            'details.variant.concentration',
            'warehouse',
            'user',
        ])->findOrFail($id);

        // Lấy tất cả kho để chọn
        $warehouses = Warehouse::all();

        return view('admin.orders.show', compact('order', 'warehouses'));
    }

    /**
     * Gán kho – Chỉ 1 lần
     */
    public function updateWarehouse(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->warehouse_id) {
            return back()->withErrors('Đơn hàng đã được gán kho, không thể thay đổi');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
        ]);

        $order->update([
            'warehouse_id' => $request->warehouse_id,
        ]);

        return back()->with('success', 'Đã chọn kho xuất hàng');
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus(Request $request, $id, StockService $stockService)
    {
        $order = Order::with('details')->findOrFail($id);

        // Lấy trạng thái mới
        $newStatus = $request->input('order_status');

        // Không cho null
        if (! $newStatus) {
            return back()->withErrors('Trạng thái đơn hàng không hợp lệ');
        }

        // Bắt buộc chọn kho trước khi PREPARING
        if (
            $newStatus === OrderStatusHelper::PREPARING &&
            ! $order->warehouse_id
        ) {
            return back()->withErrors('Vui lòng chọn kho trước khi chuẩn bị hàng');
        }

        // Kiểm tra trạng thái có thể cập nhật
        if (! OrderStatusHelper::canUpdateStatus($order->order_status, $newStatus)) {
            return back()->withErrors('Không thể chuyển trạng thái này');
        }

        DB::transaction(function () use ($order, $newStatus, $stockService, $request) {
            // Trừ kho khi chuyển từ PENDING → PREPARING
            if (
                $order->order_status === OrderStatusHelper::PENDING &&
                $newStatus === OrderStatusHelper::PREPARING
            ) {
                try {
                    $stockService->exportByOrder($order);
                } catch (\Exception $e) {
                    return back()->withErrors($e->getMessage());
                }
            }
            // Hủy → hoàn kho
            if ($newStatus === OrderStatusHelper::CANCELLED) {
                $stockService->cancelOrder($order);

                $order->update([
                    'cancellation_reason' => $request->input('cancellation_reason'),
                    'cancelled_at'        => now(),
                ]);
            }
            // Cập nhật trạng thái
            $order->update([
                'order_status' => $newStatus,
            ]);
        });
        return back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
