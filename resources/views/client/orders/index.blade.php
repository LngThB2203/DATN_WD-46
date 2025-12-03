@extends('client.layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn hàng của tôi</li>
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

        @if(!auth()->check())
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">Tìm đơn hàng</h6>
                <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="phone" class="form-control" placeholder="Số điện thoại" value="{{ request('phone') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Sản phẩm</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ str_pad($order->id,6,'0',STR_PAD_LEFT) }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->details->count() }} sản phẩm</td>
                        <td>{{ number_format($order->grand_total ?? $order->total_price,0,',','.') }} đ</td>
                        <td>
                            @php
                                $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                                $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size:4rem;color:#ccc;"></i>
            <p class="text-muted mt-3 mb-4">Bạn chưa có đơn hàng nào</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
        @endif
    </div>
</section>
@endsection
