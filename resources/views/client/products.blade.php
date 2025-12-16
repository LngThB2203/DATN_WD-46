@extends('client.layouts.app')

@section('title', 'Sản phẩm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h3 class="mb-0">Tất cả sản phẩm</h3>

            <form method="GET" action="{{ route('client.products.index') }}" class="d-flex flex-wrap gap-2">
                <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Tìm kiếm sản phẩm..." style="min-width: 220px;">

                <select name="sort" class="form-select" style="min-width: 180px;">
                    <option value="">Mặc định</option>
                    <option value="new" {{ ($sort ?? '') === 'new' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="price_asc" {{ ($sort ?? '') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                    <option value="price_desc" {{ ($sort ?? '') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                </select>

                <button type="submit" class="btn btn-primary">Lọc</button>
            </form>
        </div>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img 
                                src="{{ $product->primaryImageModel 
                                        ? asset('storage/' . $product->primaryImageModel->image_path)
                                        : asset('assets/client/img/product/product-1.webp') }}"
                                class="card-img-top"
                                style="height: 250px; object-fit: cover;"
                                alt="{{ $product->name }}">
                        </a>

                        <div class="card-body d-flex flex-column">
                            <h6 class="mb-2">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h6>

                            <div class="mb-2">
                                @if($product->sale_price)
                                    <span class="text-primary fw-bold">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</span>
                                    <span class="text-muted text-decoration-line-through ms-2">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                @else
                                    <span class="text-primary fw-bold">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                @endif
                            </div>

                            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-sm btn-outline-primary mt-auto w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">Chưa có sản phẩm nào.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</section>
@endsection
