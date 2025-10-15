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
        <h2 class="fw-bold mb-3 text-success">Cảm ơn bạn!</h2>
        <p class="mb-4">Đơn hàng của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ sớm.</p>
        <a class="btn btn-primary" href="{{ route('home') }}">Tiếp tục mua sắm</a>
    </div>
</section>
@endsection
