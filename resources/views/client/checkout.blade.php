@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header fw-semibold">Thông tin giao hàng</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input class="form-control" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa chỉ</label>
                                <input class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tỉnh/Thành phố</label>
                                <input class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quận/Huyện</label>
                                <input class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header fw-semibold">Phương thức thanh toán</div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" id="cod" checked>
                            <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" id="bank">
                            <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header fw-semibold">Đơn hàng</div>
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
                        <button class="btn btn-primary w-100">Đặt hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
