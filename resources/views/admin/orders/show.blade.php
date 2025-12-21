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
                                <th style="width: 80px;">Hình ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Giá</th>
                                <th>SL</th>
                                <th>Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->details as $item)
                            @php
                            $product = $item->product;
                            $primaryImage = $product ? ($product->galleries->where('is_primary', true)->first() ??
                            $product->galleries->first()) : null;
                            $imageUrl = $primaryImage
                            ? asset('storage/' . $primaryImage->image_path)
                            : asset('assets/client/img/product/product-1.webp');
                            @endphp
                            <tr>
                                <td>
                                    <img src="{{ $imageUrl }}" class="rounded border"
                                        style="width:60px;height:60px;object-fit:cover"
                                        onerror="this.src='{{ asset('assets/client/img/product/product-1.webp') }}'">
                                </td>
                                <td>{{ $product->name ?? 'N/A' }}</td>
                                <td>
                                    @if($item->variant)
                                    Size: {{ $item->variant->size->size_name ?? '' }}<br>
                                    Hương: {{ $item->variant->scent->scent_name ?? '' }}<br>
                                    Nồng độ: {{ $item->variant->concentration->concentration_name ?? '' }}
                                    @else
                                    —
                                    @endif
                                </td>
                                <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <strong>
                                        {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',',
                                        '.') }} đ
                                    </strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Không có sản phẩm nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tổng tiền --}}
        <div class="card mb-4">
            <div class="card-header">Tổng tiền đơn hàng</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-end"><strong>Tạm tính:</strong></td>
                                    <td class="text-end">
                                        {{ number_format($order->subtotal ?? $order->details->sum(fn($i) => $i->price *
                                        $i->quantity), 0, ',', '.') }} đ
                                    </td>
                                </tr>
                                @if($order->shipping_cost > 0)
                                <tr>
                                    <td class="text-end"><strong>Phí vận chuyển:</strong></td>
                                    <td class="text-end">{{ number_format($order->shipping_cost, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($order->discount_total > 0)
                                <tr>
                                    <td class="text-end"><strong>Giảm giá:</strong></td>
                                    <td class="text-end text-danger">
                                        - {{ number_format($order->discount_total, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="text-end"><strong class="fs-5">Tổng cộng:</strong></td>
                                    <td class="text-end">
                                        <strong class="fs-5 text-primary">
                                            {{ number_format($order->grand_total ?? $order->total_price, 0, ',', '.') }}
                                            đ
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kho xuất hàng --}}
        <div class="card mb-4">
            <div class="card-header">Kho xuất hàng</div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    Kho:
                    <strong>{{ $order->warehouse->warehouse_name }}</strong>
                </div>
            </div>
        </div>

        {{-- Trạng thái --}}
        {{-- Trạng thái --}}
@php
    use App\Helpers\OrderStatusHelper;

    $currentStatus = OrderStatusHelper::mapOldStatus($order->order_status);
    $statusName  = OrderStatusHelper::getStatusName($currentStatus);
    $statusClass = OrderStatusHelper::getStatusBadgeClass($currentStatus);
@endphp

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <span>Trạng thái đơn hàng</span>
        <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
            @csrf
            @method('PUT')

            <select name="order_status" class="form-select mb-2" required>
                @foreach (OrderStatusHelper::getStatuses() as $key => $label)
                    @if (OrderStatusHelper::canUpdateStatus($currentStatus, $key))
                        <option value="{{ $key }}">
                            {{ $label }}
                        </option>
                    @endif
                @endforeach
            </select>

            <button class="btn btn-success">
                Cập nhật trạng thái
            </button>
        </form>
    </div>
</div>


    </div>
</div>
@endsection
