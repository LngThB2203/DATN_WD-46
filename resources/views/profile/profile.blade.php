@extends('client.layouts.client')

@section('title', 'My account')

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- CỘT TRÁI: THÔNG TIN TÀI KHOẢN --}}
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-person"></i> THÔNG TIN TÀI KHOẢN
                    </h5>
                    <a href="{{ route('account.edit') }}" class="text-decoration-none">
                        Chỉnh sửa
                    </a>
                </div>

                <div>
                    <p>
                        <strong>Họ và tên:</strong>
                        {{ Auth::user()->name ?? 'Chưa có' }}
                    </p>

                    <div class="d-flex align-items-center flex-wrap mb-2">
                        <strong class="me-1">Email:</strong>
                        <span>{{ Auth::user()->email }}</span>

                        @if(Auth::user()->hasVerifiedEmail())
                            <span class="badge bg-success ms-2"> Đã xác thực</span>
                        @else
                            <form action="{{ route('verification.send') }}" method="POST" class="ms-2 d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm btn-outline-primary py-0 px-2"
                                    style="font-size: 0.8rem;">
                                    Xác thực email
                                </button>
                            </form>
                        @endif
                    </div>

                    <p>
                        <strong>Giới tính:</strong>
                        {{ Auth::user()->gender === 'male'
                            ? 'Nam'
                            : (Auth::user()->gender === 'female' ? 'Nữ' : 'Chưa có') }}
                    </p>

                    <p>
                        <strong>Số điện thoại:</strong>
                        {{ Auth::user()->phone ?? 'Chưa có' }}
                    </p>

                    <p>
                        <strong>Địa chỉ:</strong>
                        {{ Auth::user()->address ?? 'Chưa có' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: LỊCH SỬ MUA HÀNG --}}
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded">
                @php
                    use App\Models\Order;
                    $orders = Order::where('user_id', auth()->id())
                        ->where('order_status', 'completed')
                        ->orderBy('created_at', 'desc')
                        ->get();

                    function translateStatus($status) {
                        return match($status) {
                            'completed' => 'Hoàn thành',
                            default => $status,
                        };
                    }
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> LỊCH SỬ MUA HÀNG
                    </h5>
                    <a href="{{ route('orders.index') }}" class="text-decoration-none">
                        Xem tất cả
                    </a>
                </div>

                <div style="max-height: 650px; overflow-y: auto;">
                    @if($orders->count() == 0)
                        <p>Bạn chưa có đơn hàng nào. Tiếp tục mua hàng!</p>
                    @else
                        @foreach($orders as $order)
                            <div class="mb-3 p-3 border rounded bg-light">
                                {{-- Thông tin đơn hàng --}}
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Mã đơn: #{{ $order->id }}</strong>
                                    <span class="text-success">
                                        {{ translateStatus($order->order_status) }}
                                    </span>
                                </div>

                                {{-- Danh sách sản phẩm --}}
                                @foreach($order->details as $detail)
                                    <div class="d-flex mb-2">
                                        @php
                                            $primaryImage = $detail->product->galleries
                                                ->where('is_primary', true)
                                                ->first();
                                        @endphp

                                        <img
                                            src="{{ $primaryImage
                                                ? asset('storage/' . $primaryImage->image_path)
                                                : asset('images/no-image.png') }}"
                                            style="width: 80px; height: 80px; object-fit: cover;"
                                            class="rounded border me-3"
                                        >

                                        <div>
                                            <div class="fw-bold">
                                                {{ $detail->product->name }}
                                            </div>
                                            <div>
                                                Giá: {{ number_format($detail->price) }}₫
                                            </div>
                                            <div>
                                                Số lượng: x{{ $detail->quantity }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Tổng đơn hàng --}}
                                <div class="mt-2 border-top pt-2 fw-bold">
                                    Tổng tiền: {{ number_format($order->grand_total) }}₫
                                </div>

                                <a href="{{ route('orders.show', $order->id) }}" class="text-primary">
                                    Xem chi tiết
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ĐĂNG XUẤT --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-outline-dark">
            ĐĂNG XUẤT
        </button>
    </form>
</div>
@endsection
