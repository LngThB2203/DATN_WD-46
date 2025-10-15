@extends('client.layouts.app')

@section('title', $slug ?? 'Chi tiết sản phẩm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $slug }}</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <img src="{{ asset('assets/client/img/product/product-1.webp') }}" class="img-fluid rounded" alt="{{ $slug }}">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-capitalize">{{ str_replace('-', ' ', $slug) }}</h2>
                <p class="text-muted">Mô tả ngắn sản phẩm. Hương thơm tinh tế, lưu hương lâu.</p>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="fs-3 fw-semibold text-primary">$89.99</span>
                    <span class="text-decoration-line-through text-muted">$109.99</span>
                </div>
                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-outline-secondary">-</button>
                    <input type="number" class="form-control w-auto" value="1" min="1">
                    <button class="btn btn-outline-secondary">+</button>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-primary">Thêm vào giỏ</button>
                    <button class="btn btn-outline-primary">Mua ngay</button>
                </div>
            </div>
        </div>
        <div class="mt-5">
            <h4 class="mb-3">Mô tả chi tiết</h4>
            <p>Đây là phần mô tả chi tiết sản phẩm. Sau này sẽ hiển thị nội dung động từ cơ sở dữ liệu.</p>
        </div>
    </div>
</section>
@endsection
