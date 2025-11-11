@extends('client.layouts.app')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                @php
                    $primary = $galleries->where('is_primary', true)->first() ?? $galleries->first();
                @endphp
                @if($primary)
                    <a href="{{ asset('storage/' . $primary->image_path) }}" class="glightbox" data-gallery="product">
                        <img id="mainImage" src="{{ asset('storage/' . $primary->image_path) }}" class="img-fluid rounded w-100" alt="{{ $primary->alt_text ?? $product->name }}">
                    </a>
                @elseif($product->image)
                    <a href="{{ asset('storage/' . $product->image) }}" class="glightbox" data-gallery="product">
                        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded w-100" alt="{{ $product->name }}">
                    </a>
                @else
                    <img id="mainImage" src="{{ asset('assets/client/img/product/product-1.webp') }}" class="img-fluid rounded w-100" alt="{{ $product->name }}">
                @endif

                @if($galleries->count())
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        @foreach($galleries as $item)
                            <a href="{{ asset('storage/' . $item->image_path) }}" class="glightbox" data-gallery="product"
                               data-large="{{ asset('storage/' . $item->image_path) }}">
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->alt_text ?? $product->name }}" class="rounded border" style="width: 84px; height: 84px; object-fit: cover;">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-capitalize">{{ $product->name }}</h2>
                <p class="text-muted">{{ $product->brand ? 'Thương hiệu: ' . $product->brand : '' }}</p>
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->formatted_sale_price)
                        <span class="fs-3 fw-semibold text-primary">{{ $product->formatted_sale_price }}</span>
                        <span class="text-decoration-line-through text-muted">{{ $product->formatted_price }}</span>
                        @if($product->discount_percentage)
                            <span class="badge bg-danger">-{{ $product->discount_percentage }}%</span>
                        @endif
                    @else
                        <span class="fs-3 fw-semibold text-primary">{{ $product->formatted_price }}</span>
                    @endif
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
            <p>{{ $product->description }}</p>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.GLightbox) {
            GLightbox({ selector: '.glightbox' });
        }
        var mainImage = document.getElementById('mainImage');
        var thumbs = document.querySelectorAll('[data-gallery="product"][data-large]');
        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function (e) {
                e.preventDefault();
                var large = this.getAttribute('data-large');
                var wrapper = mainImage.closest('a.glightbox');
                if (wrapper) {
                    wrapper.setAttribute('href', large);
                }
                mainImage.setAttribute('src', large);
            });
        });
    });

</script>
@endsection
