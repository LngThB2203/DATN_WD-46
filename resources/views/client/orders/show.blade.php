@extends('client.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Mã đơn hàng: <strong>#{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</strong></h5>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                'awaiting_payment' => 'secondary'
                            ];
                            $statusLabels = [
                                'pending' => 'Chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'shipped' => 'Đang giao hàng',
                                'delivered' => 'Đã giao hàng',
                                'cancelled' => 'Đã hủy',
                                'awaiting_payment' => 'Chờ thanh toán'
                            ];
                            $color = $statusColors[$order->order_status] ?? 'secondary';
                            $label = $statusLabels[$order->order_status] ?? $order->order_status;
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">{{ $label }}</span>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Sản phẩm</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $detail)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($detail->product && $detail->product->primaryImage())
                                                        <img src="{{ asset('storage/' . $detail->product->primaryImage()->image_path) }}" 
                                                             alt="{{ $detail->product->name }}" 
                                                             class="rounded" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $detail->product->name ?? 'Sản phẩm đã bị xóa' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($detail->price, 0, ',', '.') }} đ</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td><strong>{{ number_format($detail->subtotal, 0, ',', '.') }} đ</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                        @if($order->customer_email)
                            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                        @endif
                        <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                        @if($order->customer_note)
                            <p><strong>Ghi chú:</strong> {{ $order->customer_note }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->subtotal ?? $order->total_price, 0, ',', '.') }} đ</span>
                        </div>
                        @if($order->discount_total > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Giảm giá</span>
                                <span>-{{ number_format($order->discount_total, 0, ',', '.') }} đ</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }} đ</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold mb-3">
                            <span>Tổng cộng</span>
                            <span class="text-primary fs-5">{{ number_format($order->grand_total ?? $order->total_price, 0, ',', '.') }} đ</span>
                        </div>

                        <div class="mb-3">
                            <strong>Phương thức thanh toán:</strong>
                            <p class="mb-0">
                                @if($order->payment_method === 'cod')
                                    Thanh toán khi nhận hàng (COD)
                                @elseif($order->payment_method === 'bank_transfer')
                                    Chuyển khoản ngân hàng
                                @else
                                    {{ $order->payment_method }}
                                @endif
                            </p>
                        </div>

                        @if($order->payment && $order->payment_method === 'bank_transfer')
                            <div class="alert alert-info">
                                <strong>Thông tin chuyển khoản:</strong><br>
                                Số tài khoản: 1234567890<br>
                                Ngân hàng: ABC Bank<br>
                                Chủ tài khoản: Công ty TNHH ABC<br>
                                Nội dung: Thanh toán đơn hàng #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

