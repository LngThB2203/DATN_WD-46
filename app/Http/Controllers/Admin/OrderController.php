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

        $orders         = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $statuses       = OrderStatusHelper::getStatuses();
        $selectedStatus = $request->status;

        return view('admin.orders.list', compact('orders', 'statuses', 'selectedStatus'));
    }

    // Tự động gán kho nếu chưa có
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

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id, StockService $stockService)
    {
        $order     = Order::with('details')->findOrFail($id);
        $newStatus = $request->input('order_status');

        if (! $newStatus) {
            return back()->withErrors('Trạng thái đơn hàng không hợp lệ');
        }

        // Bắt buộc chọn kho trước khi PREPARING
        if ($newStatus === OrderStatusHelper::PREPARING && ! $order->warehouse_id) {
            return back()->withErrors('Vui lòng chọn kho trước khi chuẩn bị hàng');
        }

        // Kiểm tra trạng thái có thể cập nhật
        if (! OrderStatusHelper::canUpdateStatus($order->order_status, $newStatus)) {
            return back()->withErrors(
                'Không thể chuyển từ trạng thái "' .
                OrderStatusHelper::getStatusName($order->order_status) .
                '" sang "' .
                OrderStatusHelper::getStatusName($newStatus) . '"'
            );
        }

        try {
            DB::beginTransaction();

            // Trừ kho khi PENDING → PREPARING
            $currentStatus = OrderStatusHelper::mapOldStatus($order->order_status);
            if (
                ($order->order_status === OrderStatusHelper::PENDING || $currentStatus === OrderStatusHelper::PENDING) &&
                $newStatus === OrderStatusHelper::PREPARING
            ) {
                try {
                    $stockService->exportByOrder($order);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->withErrors($e->getMessage());
                }
            }

            // Hủy → hoàn kho
            if ($newStatus === OrderStatusHelper::CANCELLED) {
                try {
                    $stockService->cancelOrder($order);
                    DB::commit();
                    return back()->with('success', 'Hủy đơn hàng và hoàn kho thành công');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->withErrors('Không thể hủy đơn hàng: ' . $e->getMessage());
                }
            }

            // Cập nhật trạng thái khác
            $order->update(['order_status' => $newStatus]);

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }
}
