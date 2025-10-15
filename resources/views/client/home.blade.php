@extends('client.layouts.app')

@section('title', 'Trang chủ')

@section('content')
<section class="hero py-5">
    <div class="container-fluid container-xl">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold mb-3">Cửa hàng nước hoa trực tuyến</h2>
                <p class="lead mb-4">Khám phá bộ sưu tập nước hoa chính hãng với ưu đãi hấp dẫn. Giao nhanh, đổi trả dễ dàng.</p>
                <a href="#" class="btn btn-primary btn-lg">Mua ngay</a>
            </div>
            <div class="col-lg-6">
                <img class="img-fluid rounded" src="{{ asset('assets/client/img/product/product-1.webp') }}" alt="Hero">
            </div>
        </div>
    </div>
</section>
<section class="py-5 border-top">
    <div class="container-fluid container-xl">
        <h3 class="fw-semibold mb-4">Sản phẩm nổi bật</h3>
        <div class="row g-4">
            @for ($i = 1; $i <= 4; $i++)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100">
                        <img src="{{ asset('assets/client/img/product/product-'.$i.'.webp') }}" class="card-img-top" alt="Product {{$i}}">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Nước hoa {{ $i }}</h5>
                            <p class="card-text text-muted mb-3">$ {{ 49 + $i }}.99</p>
                            <a href="#" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
@endsection
