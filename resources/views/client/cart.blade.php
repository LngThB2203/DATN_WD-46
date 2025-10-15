@extends('client.layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header fw-semibold">Sản phẩm trong giỏ</div>
                    <div class="card-body">
                        <p>Chưa có sản phẩm. (Sẽ hiển thị dữ liệu động sau)</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header fw-semibold">Tóm tắt đơn hàng</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span><span>$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span><span>$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold mb-3">
                            <span>Tổng</span><span>$0.00</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Tiến hành thanh toán</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
