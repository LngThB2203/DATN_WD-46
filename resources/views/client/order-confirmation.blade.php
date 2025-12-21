@extends('client.layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
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
<section class="py-5 text-center">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-3 text-success">Cảm ơn bạn đã đặt hàng!</h2>

        @if(isset($order))
            <p class="mb-2">Đơn hàng <strong>#{{ $order->id }}</strong> của bạn đã được xác nhận.</p>

            <div class="card mx-auto" style="max-width:900px; text-align:left">
                <div class="card-header bg-primary text-white fw-semibold">Thông tin đơn hàng</div>
                <div class="card-body row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Thông tin giao hàng</h6>
                        <div><strong>Họ tên:</strong> {{ $order->customer_name }}</div>
                        <div><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                        <div><strong>Địa chỉ:</strong> {{ $order->shipping_address_line }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Chi tiết đơn hàng</h6>
                        <div><strong>Mã đơn hàng:</strong> #{{ $order->id }}</div>
                        <div><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
                        <div><strong>Phương thức thanh toán:</strong> {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng' : 'Thanh toán online' }}</div>
                        <div><strong>Trạng thái:</strong> {{ \App\Helpers\OrderStatusHelper::getStatusName($order->order_status) }}</div>
                    </div>

                    <div class="col-12 mt-4">
                        <h6>Sản phẩm đã đặt</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $d)
                                        <tr>
                                            <td style="max-width:420px">
                                                @if($d->product && $d->product->primaryImage())
                                                    <img src="{{ asset('storage/' . $d->product->primaryImage()->path) }}" alt="" style="height:40px; margin-right:8px; float:left">
                                                @endif
                                                <div style="overflow:hidden">{{ $d->product->name ?? 'Sản phẩm' }}</div>
                                            </td>
                                            <td class="text-end">{{ number_format($d->price, 0, ',', '.') }} đ</td>
                                            <td class="text-end">{{ $d->quantity }}</td>
                                            <td class="text-end">{{ number_format($d->subtotal, 0, ',', '.') }} đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <div style="min-width:300px">
                                <div class="d-flex justify-content-between"><span>Tạm tính</span><span>{{ number_format($order->subtotal, 0, ',', '.') }} đ</span></div>
                                <div class="d-flex justify-content-between">
                                    <span>Giảm giá @if($order->discount && $order->discount->code) (Mã: <strong>{{ $order->discount->code }}</strong>) @endif</span>
                                    <span>- {{ number_format($order->discount_total, 0, ',', '.') }} đ</span>
                                </div>
                                <div class="d-flex justify-content-between"><span>Phí vận chuyển</span><span>{{ number_format($order->shipping_cost, 0, ',', '.') }} đ</span></div>
                                <hr>
                                <div class="d-flex justify-content-between fw-semibold"><span>Tổng cộng</span><span>{{ number_format($order->grand_total, 0, ',', '.') }} đ</span></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a class="btn btn-outline-secondary" href="{{ route('orders.index') }}">Xem đơn hàng của tôi</a>
                <a class="btn btn-primary ms-2" href="{{ route('home') }}">Tiếp tục mua sắm</a>
            </div>
        @else
            <p class="mb-4">Đơn hàng đã được xác nhận. Bạn có thể xem chi tiết trong trang <a href="{{ route('orders.index') }}">Đơn hàng của tôi</a>.</p>
            <a class="btn btn-primary" href="{{ route('home') }}">Tiếp tục mua sắm</a>
        @endif
    </div>
</section>
@endsection
