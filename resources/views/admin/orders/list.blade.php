@extends('admin.layouts.admin')

@section('title', 'Danh sách đơn hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        <div class="row mb-4">
            <div class="col-xl-12 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Danh sách đơn hàng</h3>
                {{-- Thêm nút tạo mới nếu cần --}}
                {{-- <a href="#" class="btn btn-sm btn-primary">+ Thêm đơn hàng</a> --}}
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Đơn hàng</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? $order->customer_name }}</td>
                                <td>{{ number_format($order->total_price) }} đ</td>
                                <td>
                                    <span class="badge
                                        {{ $order->order_status == 'pending' ? 'bg-secondary' : '' }}
                                        {{ $order->order_status == 'processing' ? 'bg-primary' : '' }}
                                        {{ $order->order_status == 'shipping' ? 'bg-info' : '' }}
                                        {{ $order->order_status == 'completed' ? 'bg-success' : '' }}
                                        {{ $order->order_status == 'cancelled' ? 'bg-danger' : '' }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Không có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-top">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection
