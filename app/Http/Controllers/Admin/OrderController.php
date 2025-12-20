<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\OrderStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with(['user', 'details.product.galleries']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $statuses = OrderStatusHelper::getStatuses();
        $selectedStatus = $request->status;

        return view('admin.orders.list', compact('orders', 'statuses', 'selectedStatus'));
    }
    protected function autoAssignWarehouse(Order $order): void
    {
        $warehouses = Warehouse::orderBy('id')->get();

        foreach ($warehouses as $warehouse) {
            $canFulfill = true;

            foreach ($order->details as $item) {
                $stock = WarehouseProduct::where([
                    'warehouse_id' => $warehouse->id,
                    'product_id'   => $item->product_id,
                    'variant_id'   => $item->variant_id,
                ])->value('quantity') ?? 0;

                if ($stock < $item->quantity) {
                    $canFulfill = false;
                    break;
                }
            }

            if ($canFulfill) {
                $order->update([
                    'warehouse_id' => $warehouse->id,
                ]);
                return;
            }
        }

        throw new \Exception('Không có kho nào đủ hàng cho đơn này');
    }

    // Chi tiết đơn hàng
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
            'details.variant.concentration',
            'warehouse',
        ])->findOrFail($id);

        if (! $order->warehouse_id) {
            $this->autoAssignWarehouse($order);
            $order->refresh();
        }

        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái
    public function updateStatus(Request $request, $id, StockService $stockService)
    {
        $order = Order::with('details')->findOrFail($id);
        $newStatus = $request->order_status;

        if (! OrderStatusHelper::canUpdateStatus($order->order_status, $newStatus)) {
            return back()->withErrors('Không thể chuyển trạng thái này');
        }

        try {
            DB::transaction(function () use ($order, $newStatus, $stockService, $request) {

                if (
                    $order->order_status === OrderStatusHelper::PENDING &&
                    $newStatus === OrderStatusHelper::PREPARING
                ) {
                    $stockService->exportByOrder($order);
                }

                if ($newStatus === OrderStatusHelper::CANCELLED) {
                    $stockService->cancelOrder($order);

                    $order->update([
                        'cancellation_reason' => $request->cancellation_reason,
                        'cancelled_at' => now(),
                    ]);
                }

                $order->update([
                    'order_status' => $newStatus,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
