@extends('client.layouts.app')

@section('title', $category->category_name)

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->category_name }}</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">{{ $category->category_name }}</h3>

            <select class="form-select w-auto">
                <option value="">Sắp xếp</option>
                <option value="new">Mới nhất</option>
                <option value="asc">Giá tăng dần</option>
                <option value="desc">Giá giảm dần</option>
            </select>
        </div>

        <div class="row g-4">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100">

                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset($product->primaryImage->image_path ?? 'assets/client/img/no-image.jpg') }}"
                                class="card-img-top"
                                alt="{{ $product->name }}">
                        </a>

                        <div class="card-body">
                            <h6 class="mb-1">{{ $product->name }}</h6>

                            @if ($product->sale_price)
                                <p class="text-danger fw-bold mb-2">
                                    {{ number_format($product->sale_price) }}₫
                                    <del class="text-muted small d-block">{{ number_format($product->price) }}₫</del>
                                </p>
                            @else
                                <p class="text-muted mb-2">{{ number_format($product->price) }}₫</p>
                            @endif

                            <a href="{{ route('product.show', $product->slug) }}"
                                class="btn btn-sm btn-outline-primary w-100">
                                Xem chi tiết
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">Không có sản phẩm nào trong danh mục này.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</section>
@endsection
