@extends('admin.layouts.admin')

@section('title', 'Danh sách đơn hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        <div class="row mb-4">
            <div class="col-xl-12 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Danh sách đơn hàng</h3>
            </div>
        </div>

        {{-- Filter theo trạng thái --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.list') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Lọc theo trạng thái</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $selectedStatus == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($selectedStatus)
                    <div class="col-md-2">
                        <a href="{{ route('admin.orders.list') }}" class="btn btn-secondary w-100">Xóa bộ lọc</a>
                    </div>
                    @endif
                </form>
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
                            <th>Sản phẩm</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $firstDetail = $order->details->first();
                                $product = $firstDetail ? ($firstDetail->product ?? null) : null;
                                $primaryImage = $product && $product->galleries ? ($product->galleries->where('is_primary', true)->first() ?? $product->galleries->first()) : null;
                                if ($primaryImage && file_exists(storage_path('app/public/' . $primaryImage->image_path))) {
                                    $imageUrl = asset('storage/' . $primaryImage->image_path);
                                } else {
                                    $imageUrl = asset('assets/client/img/product/product-1.webp');
                                }
                            @endphp
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    @if($product)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" 
                                                 class="rounded me-2" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 onerror="this.src='{{ asset('assets/client/img/product/product-1.webp') }}'">
                                            <div>
                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                @if($order->details && $order->details->count() > 1)
                                                    <small class="text-muted">+ {{ $order->details->count() - 1 }} sản phẩm khác</small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $order->user->name ?? $order->customer_name }}</td>
                                <td>{{ number_format($order->grand_total ?? $order->total_price, 0, ',', '.') }} đ</td>
                                <td>
                                    @php
                                        $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                                        $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
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
                                <td colspan="7" class="text-center text-muted">Không có đơn hàng nào.</td>
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
