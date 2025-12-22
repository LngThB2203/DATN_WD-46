@extends('client.layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
@php
    if (!isset($order)) {
        if (session('order')) {
            $order = session('order');
        } elseif (session('last_order_id')) {
            $order = \App\Models\Order::with(['details.product', 'details.variant'])->find(session('last_order_id'));
        } elseif (auth()->check()) {
            $order = \App\Models\Order::with(['details.product', 'details.variant'])
                        ->where('user_id', auth()->id())
                        ->latest()
                        ->first();
        }
    }
@endphp

<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}">Thanh toán</a></li>
                <li class="breadcrumb-item active" aria-current="page">Xác nhận</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        @if(isset($order))
            {{-- Green Checkmark --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center" 
                     style="width:80px;height:80px;background:#28a745;border-radius:50%;color:white;font-size:50px;font-weight:bold;">
                    ✓
                </div>
            </div>

            <h2 class="text-center fw-bold mb-2" style="color:#28a745;font-size:32px;">Cảm ơn bạn đã đặt hàng!</h2>
            <p class="text-center text-muted mb-4">Đơn hàng #{{ $order->order_code ?? str_pad($order->id, 2, '0', STR_PAD_LEFT) }} của bạn đã được xác nhận.</p>

            <div class="card mx-auto shadow-sm" style="max-width:900px;">
                <div class="card-header" style="background:#0056b3;color:white;font-weight:600;padding:12px 20px;font-size:16px;">
                    Thông tin đơn hàng
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary fw-bold mb-3">Thông tin giao hàng</h6>
                            <div class="mb-2"><strong>Họ tên:</strong> {{ $order->customer_name }}</div>
                            <div class="mb-2"><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                            <div class="mb-2"><strong>Địa chỉ:</strong> {{ $order->shipping_address_line ?? $order->shipping_address }}</div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h6 class="text-primary fw-bold mb-3">Chi tiết đơn hàng</h6>
                            <div class="mb-2"><strong>Mã đơn hàng:</strong> #{{ $order->order_code ?? $order->id }}</div>
                            <div class="mb-2"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
                            <div class="mb-2">
                                <strong>Thanh toán:</strong> 
                                <span class="badge bg-info text-dark">
                                    {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng' : 'Thanh toán online' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Sản phẩm đã đặt</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Kiểm tra quan hệ details hoặc items tùy theo Model của bạn --}}
                                @php $details = $order->details ?? $order->items; @endphp
                                @foreach($details as $d)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="fw-bold text-dark">{{ $d->product->name ?? 'Sản phẩm' }}</div>
                                            </div>
                                            @if($d->variant)
                                                <small class="text-muted d-block">
                                                    Phân loại: {{ $d->variant->size->size_name ?? '' }} {{ $d->variant->scent->scent_name ?? '' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($d->price, 0, ',', '.') }} đ</td>
                                        <td class="text-center">{{ $d->quantity }}</td>
                                        <td class="text-end fw-bold">{{ number_format($d->subtotal ?? ($d->price * $d->quantity), 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-3">
                        <div class="col-md-5">
                            <table class="table table-sm table-borderless">
                                @if($order->discount_total > 0)
                                <tr>
                                    <td>Giảm giá:</td>
                                    <td class="text-end text-danger">-{{ number_format($order->discount_total, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Phí vận chuyển:</td>
                                    <td class="text-end">{{ number_format($order->shipping_cost ?? $order->shipping_fee ?? 0, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">Tổng cộng:</td>
                                    <td class="text-end fw-bold fs-5 text-primary">
                                        {{ number_format($order->grand_total ?? $order->total_amount, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pb-5">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 me-2">Quay lại trang chủ</a>
                <a href="{{ route('orders.index') }}" class="btn btn-primary px-4" style="background:#e84c89; border-color:#e84c89;">Xem lịch sử đơn hàng</a>
            </div>
        @else
            {{-- Fallback nếu không tìm thấy bất kỳ dữ liệu nào --}}
            <div class="text-center py-5">
                <h2 class="text-success fw-bold">Đặt hàng thành công!</h2>
                <p>Cảm ơn bạn đã tin tưởng. Kiểm tra email hoặc lịch sử đơn hàng để xem chi tiết.</p>
                <a class="btn btn-primary mt-3" href="{{ route('home') }}">Tiếp tục mua sắm</a>
            </div>
        @endif
    </div>
</section>
@endsection