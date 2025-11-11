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
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                Vui lòng kiểm tra lại thông tin. {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header fw-semibold">Thông tin giao hàng</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ tên *</label>
                                    <input class="form-control @error('customer_name') is-invalid @enderror"
                                           name="customer_name"
                                           value="{{ old('customer_name', $defaultCustomer['name'] ?? '') }}">
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input class="form-control @error('customer_email') is-invalid @enderror"
                                           name="customer_email"
                                           value="{{ old('customer_email', $defaultCustomer['email'] ?? '') }}">
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại *</label>
                                    <input class="form-control @error('customer_phone') is-invalid @enderror"
                                           name="customer_phone"
                                           value="{{ old('customer_phone', $defaultCustomer['phone'] ?? '') }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Địa chỉ chi tiết *</label>
                                    <input class="form-control @error('shipping_address_line') is-invalid @enderror"
                                           name="shipping_address_line"
                                           value="{{ old('shipping_address_line') }}">
                                    @error('shipping_address_line')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phường/Xã</label>
                                    <input class="form-control @error('shipping_ward') is-invalid @enderror"
                                           name="shipping_ward"
                                           value="{{ old('shipping_ward') }}">
                                    @error('shipping_ward')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quận/Huyện *</label>
                                    <input class="form-control @error('shipping_district') is-invalid @enderror"
                                           name="shipping_district"
                                           value="{{ old('shipping_district') }}">
                                    @error('shipping_district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tỉnh/Thành phố *</label>
                                    <input class="form-control @error('shipping_province') is-invalid @enderror"
                                           name="shipping_province"
                                           value="{{ old('shipping_province') }}">
                                    @error('shipping_province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Ghi chú cho đơn hàng</label>
                                    <textarea class="form-control @error('customer_note') is-invalid @enderror"
                                              name="customer_note"
                                              rows="3">{{ old('customer_note') }}</textarea>
                                    @error('customer_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header fw-semibold">Phương thức thanh toán</div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="payment_method"
                                       id="payment_cod"
                                       value="cod"
                                       {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_cod">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="payment_method"
                                       id="payment_bank"
                                       value="bank_transfer"
                                       {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_bank">
                                    Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                            <span>Đơn hàng</span>
                            <span class="badge bg-secondary">{{ count($cart['items']) }} sản phẩm</span>
                        </div>
                        <div class="card-body">
                            @if(!empty($cart['items']))
                                <div class="mb-3">
                                    @foreach($cart['items'] as $item)
                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                            <div>
                                                <div class="fw-semibold">{{ $item['name'] ?? 'Sản phẩm' }}</div>
                                                <small class="text-muted">x{{ $item['quantity'] ?? 1 }}</small>
                                            </div>
                                            <div class="text-end">
                                                <div>{{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }} đ</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-3">Giỏ hàng của bạn đang trống.</p>
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính</span>
                                <span>{{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá</span>
                                <span>- {{ number_format($cart['discount_total'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển</span>
                                <span>{{ number_format($cart['shipping_fee'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-semibold mb-3">
                                <span>Tổng cộng</span>
                                <span>{{ number_format($cart['grand_total'] ?? 0, 0, ',', '.') }} đ</span>
                            </div>

                            @error('cart')
                                <div class="alert alert-danger py-2">{{ $message }}</div>
                            @enderror

                            <button class="btn btn-primary w-100" type="submit">Đặt hàng</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
