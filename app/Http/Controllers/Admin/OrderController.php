<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with(['user', 'details.product.galleries']);

        // Filter theo trạng thái
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query->orderBy('id', 'DESC')->paginate(15)->withQueryString();

        // Lấy danh sách trạng thái để hiển thị trong filter
        $statuses = \App\Helpers\OrderStatusHelper::getStatuses();
        $selectedStatus = $request->status ?? null;

        return view('admin.orders.list', compact('orders', 'statuses', 'selectedStatus'));
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'user',
            'discount',
            'payment',
            'shipment',
            'details.product.galleries',
            'details.variant.size',
            'details.variant.scent',
            'details.variant.concentration'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $rules = [
            'order_status' => 'required|in:pending,preparing,awaiting_pickup,delivered,cancelled,completed',
        ];
        
        // Nếu chọn hủy đơn hàng, lý do hủy là bắt buộc
        if ($request->order_status === 'cancelled') {
            $rules['cancellation_reason'] = 'required|string|max:500';
        } else {
            $rules['cancellation_reason'] = 'nullable|string|max:500';
        }
        
        $request->validate($rules, [
            'cancellation_reason.required' => 'Vui lòng nhập lý do hủy đơn hàng.',
            'cancellation_reason.max' => 'Lý do hủy không được vượt quá 500 ký tự.',
        ]);

        $order = Order::with(['details.product', 'details.variant', 'payment'])->findOrFail($id);
        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;
        
        // Kiểm tra xem có thể cập nhật trạng thái không
        if (!\App\Helpers\OrderStatusHelper::canUpdateStatus($oldStatus, $newStatus)) {
            return back()->with('error', 'Không thể chuyển đổi từ trạng thái "' . \App\Helpers\OrderStatusHelper::getStatusName($oldStatus) . '" sang "' . \App\Helpers\OrderStatusHelper::getStatusName($newStatus) . '".');
        }

        DB::beginTransaction();
        try {
            // Nếu hủy đơn hàng, cần hoàn trả tồn kho
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->restoreInventory($order);
                
                // Cập nhật trạng thái thanh toán nếu đã thanh toán
                if ($order->payment) {
                    $order->payment->update([
                        'status' => 'refunded',
                        'refunded_at' => now(),
                    ]);
                }
                
                // Lưu lý do hủy
                $order->update([
                    'order_status' => $newStatus,
                    'cancellation_reason' => $request->cancellation_reason ?? 'Đơn hàng bị hủy bởi quản trị viên',
                    'cancelled_at' => now(),
                ]);
            } else {
                // Cập nhật trạng thái bình thường
                $order->update([
                    'order_status' => $newStatus,
                ]);
                
                // Nếu chuyển từ cancelled sang trạng thái khác, xóa thông tin hủy
                if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    $order->update([
                        'cancellation_reason' => null,
                        'cancelled_at' => null,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating order status', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    /**
     * Hoàn trả tồn kho khi hủy đơn hàng
     */
    private function restoreInventory(Order $order)
    {
        foreach ($order->details as $detail) {
            if (!$detail->product_id) {
                continue;
            }

            // Tìm warehouse product để hoàn trả
            // Nếu có variant, tìm theo variant_id, không thì tìm theo product_id
            $warehouseProduct = null;
            
            if ($detail->variant_id) {
                // Tìm warehouse product theo variant
                $warehouseProduct = \App\Models\WarehouseProduct::where('product_id', $detail->product_id)
                    ->where('variant_id', $detail->variant_id)
                    ->first();
            } else {
                // Tìm warehouse product không có variant (variant_id = null)
                $warehouseProduct = \App\Models\WarehouseProduct::where('product_id', $detail->product_id)
                    ->whereNull('variant_id')
                    ->first();
            }

            if ($warehouseProduct) {
                // Hoàn trả số lượng
                $warehouseProduct->increment('quantity', $detail->quantity);
                $warehouseProduct->update(['last_updated' => now()]);
                
                Log::info('Restored inventory for cancelled order', [
                    'order_id' => $order->id,
                    'product_id' => $detail->product_id,
                    'variant_id' => $detail->variant_id,
                    'quantity' => $detail->quantity,
                ]);
            } else {
                Log::warning('Could not find warehouse product to restore', [
                    'order_id' => $order->id,
                    'product_id' => $detail->product_id,
                    'variant_id' => $detail->variant_id,
                ]);
            }
        }
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
