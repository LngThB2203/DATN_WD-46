@extends('client.layouts.app')

@section('title', 'Trang chủ')

@section('content')
<main class="main">

    <!-- Hero Section -->
    <section class="hero py-5">
        <div class="container-fluid container-xl">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-3">Cửa hàng nước hoa trực tuyến</h2>
                    <p class="lead mb-4">
                        Khám phá bộ sưu tập nước hoa chính hãng với ưu đãi hấp dẫn.
                        Giao nhanh, đổi trả dễ dàng.
                    </p>
                    <a href="#" class="btn btn-primary btn-lg">Mua ngay</a>
                </div>
                <div class="col-lg-6">
                    @if($heroProduct && $heroProduct->primaryImage())
                        <img class="img-fluid rounded"
                             src="{{ asset('storage/' . $heroProduct->primaryImage()->image_path) }}"
                             alt="Hero">
                    @else
                        <img class="img-fluid rounded"
                             src="{{ asset('assets/client/img/product/product-1.webp') }}"
                             alt="Hero">
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Info Cards Section -->
    <section id="info-cards" class="info-cards section light-background">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="info-card text-center">
                        <div class="icon-box"><i class="bi bi-truck"></i></div>
                        <h3>Free Shipping</h3>
                        <p>Giao hàng nhanh, tận nơi, đảm bảo an toàn.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="info-card text-center">
                        <div class="icon-box"><i class="bi bi-piggy-bank"></i></div>
                        <h3>Money Back Guarantee</h3>
                        <p>Hoàn tiền nếu sản phẩm không đúng mô tả.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="info-card text-center">
                        <div class="icon-box"><i class="bi bi-percent"></i></div>
                        <h3>Discount Offers</h3>
                        <p>Ưu đãi hấp dẫn dành cho khách hàng thân thiết.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="info-card text-center">
                        <div class="icon-box"><i class="bi bi-headset"></i></div>
                        <h3>24/7 Support</h3>
                        <p>Hỗ trợ khách hàng mọi lúc mọi nơi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Cards Section -->
    <section id="category-cards" class="category-cards section">
        <div class="container">
            <div class="category-slider swiper init-swiper">
                <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "autoplay": {"delay":5000,"disableOnInteraction":false},
                        "grabCursor": true,
                        "speed": 600,
                        "slidesPerView": "auto",
                        "spaceBetween": 20,
                        "navigation": {"nextEl": ".swiper-button-next","prevEl": ".swiper-button-prev"}
                    }
                </script>
                <div class="swiper-wrapper">
                    @foreach($categories as $category)
                        <div class="swiper-slide">
                            <div class="category-card">
                                <div class="category-image">
                                    <img src="{{ asset($category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="img-fluid">
                                </div>
                                <h3 class="category-title">{{ $category->name }}</h3>
                                <p class="category-count">
                                    {{ $category->products_count ?? $category->products->count() }} Products
                                </p>
                                <a href="#" class="stretched-link"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-5 border-top">
        <div class="container-fluid container-xl">
            <h3 class="fw-semibold mb-4">Sản phẩm nổi bật</h3>
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card h-100">
                            @if($product->primaryImage())
                                <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}"
                                     class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <img src="{{ asset('assets/client/img/product/product-1.webp') }}"
                                     class="card-img-top" alt="{{ $product->name }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title mb-1">{{ $product->name }}</h5>
                                <p class="card-text text-muted mb-3">
                                    {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                </p>
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="btn btn-outline-primary w-100">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Best Sellers Section -->
    <section id="best-sellers" class="best-sellers section">
        <div class="container section-title">
            <h2>Best Sellers</h2>
            <p>Những sản phẩm bán chạy nhất của chúng tôi</p>
        </div>
        <div class="container">
            <div class="row gy-4">
                @foreach($products as $product)
                    <div class="col-md-6 col-lg-3">
                        <div class="product-card">
                            <div class="product-image">
                                @if($product->primaryImage())
                                    <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}"
                                         class="img-fluid default-image"
                                         alt="{{ $product->name }}">
                                @else
                                    <img src="{{ asset('assets/client/img/product/product-1.webp') }}"
                                         class="img-fluid default-image"
                                         alt="{{ $product->name }}">
                                @endif
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="#">{{ $product->name }}</a>
                                </h3>
                                <div class="product-price">
                                    {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</main>
@endsection
