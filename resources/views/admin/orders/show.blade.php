@extends('admin.layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <h3 class="mb-4">Chi tiết đơn hàng #{{ $order->id }}</h3>

        {{-- Thông tin khách hàng --}}
        <div class="card mb-4">
            <div class="card-header">Thông tin khách hàng</div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $order->user->name ?? $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email ?? $order->customer_email }}</p>
                <p><strong>SĐT:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
            </div>
        </div>

        {{-- Sản phẩm --}}
        <div class="card mb-4">
            <div class="card-header">Sản phẩm trong đơn</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Giá</th>
                                <th>SL</th>
                                <th>Tồn kho (Kho đã chọn)</th>
                                <th>Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->details as $item)
                                @php
                                    $selectedWarehouse = $order->warehouse_id ? $order->warehouse : null;
                                    $stock = $selectedWarehouse
                                        ? $item->variant
                                            ? $item->variant->warehouseStock->where('warehouse_id', $selectedWarehouse->id)->sum('quantity')
                                            : $item->product->warehouseProducts->where('warehouse_id', $selectedWarehouse->id)->sum('quantity')
                                        : null;
                                @endphp
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        @if($item->variant)
                                            Size: {{ $item->variant->size->size_name ?? '' }}<br>
                                            Hương: {{ $item->variant->scent->scent_name ?? '' }}<br>
                                            Nồng độ: {{ $item->variant->concentration->concentration_name ?? '' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price) }} đ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        @if($stock !== null)
                                            <span class="{{ $stock < $item->quantity ? 'text-danger fw-bold' : '' }}">
                                                {{ $stock }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price * $item->quantity) }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Chọn kho --}}
        <div class="card mb-4">
            <div class="card-header">Kho xuất hàng</div>
            <div class="card-body">
                @if (!$order->warehouse_id)
                    <form method="POST" action="{{ route('admin.orders.update-warehouse', $order->id) }}">
                        @csrf
                        @method('PUT')
                        <label class="form-label">Chọn kho</label>
                        <select name="warehouse_id" class="form-select mb-2" required>
                            <option value="">-- Chọn kho --</option>
                            @foreach ($warehouses as $warehouse)
                                @php
                                    $warning = false;
                                    foreach ($order->details as $item) {
                                        $stock = $item->variant
                                            ? $item->variant->warehouseStock->where('warehouse_id', $warehouse->id)->sum('quantity')
                                            : $item->product->warehouseProducts->where('warehouse_id', $warehouse->id)->sum('quantity');
                                        if ($stock < $item->quantity) {
                                            $warning = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <option value="{{ $warehouse->id }}" {{ $warning ? 'disabled' : '' }}>
                                    {{ $warehouse->warehouse_name }}
                                    @if($warning) - không đủ hàng @endif
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary">Xác nhận kho</button>
                    </form>
                @else
                    <div class="alert alert-info mb-0">
                        Kho: <strong>{{ $order->warehouse->warehouse_name }}</strong>
                    </div>
                @endif
            </div>
        </div>

        {{-- Trạng thái --}}
        @if ($order->warehouse_id)
            @php
                $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
            @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Trạng thái đơn hàng</span>
                    <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                        @csrf
                        @method('PUT')
                        <select name="order_status" class="form-select mb-2" required>
                            @foreach (\App\Helpers\OrderStatusHelper::getStatuses() as $key => $label)
                                @if (\App\Helpers\OrderStatusHelper::canUpdateStatus($order->order_status, $key))
                                    <option value="{{ $key }}" {{ $order->order_status == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <button class="btn btn-success">Cập nhật trạng thái</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
