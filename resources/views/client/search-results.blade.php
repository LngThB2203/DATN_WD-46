@extends('client.layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h3 class="fw-semibold mb-4">Kết quả cho: <em>{{ request('q') }}</em></h3>
        <div class="row g-4">
            @for ($i = 1; $i <= 6; $i++)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100">
                        <img src="{{ asset('assets/client/img/product/product-'.(($i-1)%6+1).'.webp') }}" class="card-img-top" alt="Result {{$i}}">
                        <div class="card-body">
                            <h6 class="mb-1">Sản phẩm kết quả {{ $i }}</h6>
                            <p class="text-muted">$ {{ 49 + $i }}.99</p>
                            <a href="{{ route('product.show', ['slug' => 'nuoc-hoa-'.$i]) }}" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
@endsection
