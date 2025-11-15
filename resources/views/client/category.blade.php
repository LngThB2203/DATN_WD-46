@extends('client.layouts.app')

@section('title', 'Danh mục sản phẩm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh mục</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-semibold">Bộ lọc</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="range" class="form-range">
                        </div>
                        <button class="btn btn-outline-primary w-100">Lọc</button>
                    </div>
                </div>
            </aside>
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Danh sách sản phẩm</h3>
                    <select class="form-select w-auto">
                        <option>Mới nhất</option>
                        <option>Giá tăng dần</option>
                        <option>Giá giảm dần</option>
                    </select>
                </div>
                <div class="row g-4">
                    @for ($i = 1; $i <= 8; $i++)
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card h-100">
                                <a href="{{ route('product.show', ['slug' => 'nuoc-hoa-'.$i]) }}">
                                    <img src="{{ asset('assets/client/img/product/product-'.(($i-1)%8+1).'.webp') }}" class="card-img-top" alt="Sản phẩm {{$i}}">
                                </a>
                                <div class="card-body">
                                    <h6 class="mb-1">Nước hoa {{ $i }}</h6>
                                    <p class="text-muted mb-2">$ {{ 59 + $i }}.99</p>
                                    <a href="{{ route('product.show', ['slug' => 'nuoc-hoa-'.$i]) }}" class="btn btn-sm btn-outline-primary w-100">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
