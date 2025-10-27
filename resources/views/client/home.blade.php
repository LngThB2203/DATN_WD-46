@extends('client.layouts.client')

@section('title', 'Trang chủ')

@section('content')
<main class="main">

    <!-- Hero Section -->
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

    <!-- Category Slider Section -->
    <section id="category-cards" class="category-cards section py-5">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="category-slider swiper init-swiper">
                <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "autoplay": {"delay": 5000,"disableOnInteraction": false},
                        "grabCursor": true,
                        "speed": 600,
                        "slidesPerView": "auto",
                        "spaceBetween": 20,
                        "navigation": {"nextEl": ".swiper-button-next","prevEl": ".swiper-button-prev"},
                        "breakpoints": {
                            "320": {"slidesPerView": 2,"spaceBetween": 15},
                            "576": {"slidesPerView": 3,"spaceBetween": 15},
                            "768": {"slidesPerView": 4,"spaceBetween": 20},
                            "992": {"slidesPerView": 5,"spaceBetween": 20},
                            "1200": {"slidesPerView": 6,"spaceBetween": 20}
                        }
                    }
                </script>
                <div class="swiper-wrapper">
                    @for ($i = 1; $i <= 8; $i++)
                    <div class="swiper-slide">
                        <div class="category-card" data-aos="fade-up" data-aos-delay="{{ $i*100 }}">
                            <div class="category-image">
                                <img src="{{ asset('assets/client/img/product/product-'.$i.'.webp') }}" alt="Category {{ $i }}" class="img-fluid">
                            </div>
                            <h3 class="category-title">Category {{ $i }}</h3>
                            <p class="category-count">{{ rand(2,12) }} Products</p>
                            <a href="#" class="stretched-link"></a>
                        </div>
                    </div>
                    @endfor
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Best Sellers Section -->
    <section id="best-sellers" class="best-sellers section py-5">
        <div class="container section-title" data-aos="fade-up">
            <h2>Best Sellers</h2>
            <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-4">
                @for ($i = 1; $i <= 8; $i++)
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $i*50 }}">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ asset('assets/client/img/product/product-'.$i.'.webp') }}" class="img-fluid default-image" alt="Product {{$i}}">
                            <div class="product-actions">
                                <button class="btn-wishlist"><i class="bi bi-heart"></i></button>
                                <button class="btn-quickview"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="#">Product {{$i}}</a></h3>
                            <div class="product-price">
                                <span class="current-price">${{ rand(40,200) }}.99</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Product List Section -->
    <section id="product-list" class="product-list section py-5">
        <div class="container" data-aos="fade-up">
            <div class="row g-4">
                @for ($i = 1; $i <= 8; $i++)
                <div class="col-md-6 col-lg-3">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ asset('assets/client/img/product/product-'.$i.'.webp') }}" alt="Product {{$i}}" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="#">Product {{$i}}</a></h5>
                            <div class="product-price">
                                <span class="current-price">${{ rand(40,200) }}.99</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

</main>
@endsection
