@extends('client.layouts.client')

@section('title', 'home')

@section('content')
<main class="main">

    <!-- Hero Section -->
    <section class="ecommerce-hero-1 hero section" id="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 content-col" data-aos="fade-right" data-aos-delay="100">
                    <div class="content">
                        <span class="promo-badge">Ưu đãi</span>
                        <h1>Khám phá bộ sưu tập mới</h1>
                        <p>Tìm sản phẩm yêu thích với nhiều khuyến mãi hấp dẫn.</p>
                        
                    </div>
                </div>
                <div class="col-lg-6 image-col" data-aos="fade-left" data-aos-delay="200">
                    <div class="hero-image">
                        @if(isset($banners) && $banners->count())
                            @php $main = $banners->first(); @endphp
                            <img src="{{ $main->image ? asset($main->image) : asset('assets/client/img/product/product-f-9.webp') }}" alt="Banner" class="main-product" loading="lazy">
                            @foreach($banners->skip(1) as $i => $bn)
                                <div class="floating-product product-{{ $i+1 }}" data-aos="fade-up" data-aos-delay="{{ 300 + $i*100 }}">
                                    <img src="{{ $bn->image ? asset($bn->image) : asset('assets/client/img/product/product-4.webp') }}" alt="Banner {{ $i+1 }}">
                                    <div class="product-info">
                                        <h4>{{ $bn->title }}</h4>
                                        @if($bn->link)
                                            <a href="{{ $bn->link }}" class="btn btn-sm btn-light">Xem</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <img src="{{asset('assets/client/img/product/product-f-9.webp')}}" alt="Hero" class="main-product" loading="lazy">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Hero Section -->

    <!-- Info Cards Section -->
    <section id="info-cards" class="info-cards section light-background">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row g-4 justify-content-center">
                <!-- Info Card 1 -->
                <div class="col-12 col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="info-card text-center">
                        <div class="icon-box">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3>Free Shipping</h3>
                        <p>Nulla sit morbi vestibulum eros duis amet, consectetur vitae lacus. Ut quis tempor felis sed
                            nunc viverra.</p>
                    </div>
                </div><!-- End Info Card 1 -->

                <!-- Info Card 2 -->
                <div class="col-12 col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="info-card text-center">
                        <div class="icon-box">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <h3>Money Back Guarantee</h3>
                        <p>Nullam gravida felis ac nunc tincidunt, sed malesuada justo pulvinar. Vestibulum nec diam
                            vitae eros.</p>
                    </div>
                </div><!-- End Info Card 2 -->

                <!-- Info Card 3 -->
                <div class="col-12 col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="info-card text-center">
                        <div class="icon-box">
                            <i class="bi bi-percent"></i>
                        </div>
                        <h3>Discount Offers</h3>
                        <p>Nulla ipsum nisi vel adipiscing amet, dignissim consectetur ornare. Vestibulum quis posuere
                            elit auctor.</p>
                    </div>
                </div><!-- End Info Card 3 -->

                <!-- Info Card 4 -->
                <div class="col-12 col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
                    <div class="info-card text-center">
                        <div class="icon-box">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3>24/7 Support</h3>
                        <p>Ipsum dolor amet sit consectetur adipiscing, nullam vitae euismod tempor nunc felis
                            vestibulum ornare.</p>
                    </div>
                </div><!-- End Info Card 4 -->
            </div>

        </div>

    </section>

    <!-- Category Cards Section -->
<section id="categories" class="section py-4 bg-light">
    <div class="container">
        <h5 class="mb-3 fw-bold text-center">Danh mục sản phẩm</h5>

        <div class="category-scroll d-flex gap-3 overflow-auto pb-2">
            @foreach($categories as $i => $cat)
                <div class="flex-shrink-0">
                    <div class="category-card p-3 text-center rounded-3 shadow-sm bg-white border"
                         data-aos="zoom-in" data-aos-delay="{{ 100 + ($i % 8) * 100 }}"
                         style="width: 160px;">
                        <h6 class="fw-semibold mb-1 text-dark">{{ $cat->name }}</h6>
                        <small class="text-secondary">{{ $cat->products_count ?? 0 }} sản phẩm</small>
                        <a href="{{ route('client.home', array_merge(request()->query(), ['category' => $cat->id])) }}"
                           class="stretched-link"></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================= SẢN PHẨM ========================= --}}
@if(request()->has('category'))
    <div class="row g-4 mt-4">
        @if($products->count() > 0)
            @foreach($products as $pro)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm text-center border-0">
                        <img src="{{ asset('uploads/' . $pro->img_thumbnail) }}" 
                             class="card-img-top rounded-top" 
                             alt="{{ $pro->name }}" 
                             style="height:220px;object-fit:cover;">
                        <div class="card-body">
                            <h6 class="card-title fw-semibold text-dark">{{ $pro->name }}</h6>
                            <p class="card-text text-muted mb-2">
                                {{ number_format($pro->price, 0, ',', '.') }}₫
                            </p>

                            @if($pro->sale_price)
                                <p class="text-danger fw-bold mb-2">
                                    Sale: {{ number_format($pro->sale_price, 0, ',', '.') }}₫
                                </p>
                            @endif

                            {{-- Nút xem chi tiết (khi có route) --}}
                            {{-- <a href="{{ route('product.show', $pro->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                Xem chi tiết
                            </a> --}}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">Chưa có sản phẩm trong danh mục này.</p>
            </div>
        @endif
    </div>
@endif






    <!-- Filter/Search Section -->
<section id="product-filter" class="section py-4 bg-light">
    <div class="container">
        <form action="{{ route('client.home') }}" method="get" class="row g-2 align-items-center justify-content-center">
            <div class="col-12 col-md-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Tìm kiếm sản phẩm...">
            </div>
            <div class="col-6 col-md-2">
                <select name="category" class="form-select">
                    <option value="">Danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(($filters['category'] ?? '') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 col-md-2">
                <input type="number" step="0.01" min="0" name="min_price" value="{{ $filters['min_price'] ?? '' }}" class="form-control" placeholder="Giá từ">
            </div>
            <div class="col-3 col-md-2">
                <input type="number" step="0.01" min="0" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="form-control" placeholder="Giá đến">
            </div>
            <div class="col-6 col-md-auto d-flex justify-content-center">
    <button class="btn btn-primary px-4 py-2 d-flex align-items-center shadow-sm" style="border-radius: 50px;">
        <i class="bi bi-search me-2"></i> Tìm kiếm
    </button>
    <div class="col-6 col-md-auto d-flex justify-content-center">
    <a href="{{ route('client.home') }}" 
       class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center shadow-sm" 
       style="border-radius: 50px;">
        <i class="bi bi-x-circle me-2"></i> Xóa lọc
    </a>
</div>
</div>


        </form>
    </div>
</section>
<!-- /Filter/Search Section -->


    <!-- New Products Section -->
    <section id="new-products" class="section">
    <div class="container">
        <div class="section-title text-center mb-4">
            <h2>Sản phẩm mới nhất</h2>
        </div>

        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm text-center">
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             class="card-img-top" 
                             alt="{{ $product->name }}" 
                             style="height:200px;object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted mb-2">
                                {{ number_format($product->price, 0, ',', '.') }}₫
                            </p>

                            @if($product->sale_price)
                                <p class="text-danger fw-bold">
                                    Sale: {{ number_format($product->sale_price, 0, ',', '.') }}₫
                                </p>
                            @endif

                            {{-- <a href="{{ route('product.show', $product->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                Xem chi tiết
                            </a> --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Hiển thị phân trang --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
</section>
<!-- /Best Sellers Section -->

    <!-- Product List Section (static demo hidden) -->
    <section id="product-list" class="product-list section d-none">

        <div class="container isotope-layout" data-aos="fade-up" data-aos-delay="100" data-default-filter="*"
            data-layout="masonry" data-sort="original-order">

            <div class="row">
                <div class="col-12">
                    <div class="product-filters isotope-filters mb-5 d-flex justify-content-center" data-aos="fade-up">
                        <ul class="d-flex flex-wrap gap-2 list-unstyled">
                            <li class="filter-active" data-filter="*">All</li>
                            <li data-filter=".filter-clothing">Clothing</li>
                            <li data-filter=".filter-accessories">Accessories</li>
                            <li data-filter=".filter-electronics">Electronics</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row product-container isotope-container" data-aos="fade-up" data-aos-delay="200">

                <!-- Product Item 1 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-clothing">
                    <div class="product-card">
                        <div class="product-image">
                            <span class="badge">Sale</span>
                            <img src="{{asset('assets/client/img/product/product-11.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-11-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Lorem ipsum dolor sit amet</a></h5>
                            <div class="product-price">
                                <span class="current-price">$89.99</span>
                                <span class="old-price">$129.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <span>(24)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 2 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-electronics">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{asset('assets/client/img/product/product-9.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-9-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Consectetur adipiscing elit</a>
                            </h5>
                            <div class="product-price">
                                <span class="current-price">$249.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                                <span>(18)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 3 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-accessories">
                    <div class="product-card">
                        <div class="product-image">
                            <span class="badge">New</span>
                            <img src="{{asset('assets/client/img/product/product-3.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-3-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Sed do eiusmod tempor</a></h5>
                            <div class="product-price">
                                <span class="current-price">$59.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                                <span>(7)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 4 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-clothing">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{asset('assets/client/img/product/product-4.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-4-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Incididunt ut labore et dolore</a>
                            </h5>
                            <div class="product-price">
                                <span class="current-price">$79.99</span>
                                <span class="old-price">$99.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <span>(32)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 5 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-electronics">
                    <div class="product-card">
                        <div class="product-image">
                            <span class="badge">Sale</span>
                            <img src="{{asset('assets/client/img/product/product-5.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-5-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Magna aliqua ut enim ad minim</a>
                            </h5>
                            <div class="product-price">
                                <span class="current-price">$199.99</span>
                                <span class="old-price">$249.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <i class="bi bi-star"></i>
                                <span>(15)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 6 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-accessories">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{asset('assets/client/img/product/product-6.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-6-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Veniam quis nostrud
                                    exercitation</a></h5>
                            <div class="product-price">
                                <span class="current-price">$45.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                                <span>(21)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 7 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-clothing">
                    <div class="product-card">
                        <div class="product-image">
                            <span class="badge">New</span>
                            <img src="{{asset('assets/client/img/product/product-7.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-7-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Ullamco laboris nisi ut aliquip</a>
                            </h5>
                            <div class="product-price">
                                <span class="current-price">$69.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <i class="bi bi-star"></i>
                                <span>(11)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

                <!-- Product Item 8 -->
                <div class="col-md-6 col-lg-3 product-item isotope-item filter-electronics">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{asset('assets/client/img/product/product-8.webp')}}" alt="Product" class="img-fluid main-img">
                            <img src="{{asset('assets/client/img/product/product-8-variant.webp')}}" alt="Product Hover"
                                class="img-fluid hover-img">
                            <div class="product-overlay">
                                <a href="cart.html" class="btn-cart"><i class="bi bi-cart-plus"></i> Add to Cart</a>
                                <div class="product-actions">
                                    <a href="#" class="action-btn"><i class="bi bi-heart"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-eye"></i></a>
                                    <a href="#" class="action-btn"><i class="bi bi-arrow-left-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><a href="product-details.html">Ex ea commodo consequat</a></h5>
                            <div class="product-price">
                                <span class="current-price">$159.99</span>
                            </div>
                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <span>(29)</span>
                            </div>
                        </div>
                    </div>
                </div><!-- End Product Item -->

            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="#" class="view-all-btn">View All Products <i class="bi bi-arrow-right"></i></a>
            </div>

        </div>

    </section><!-- /Product List Section -->

</main>
@endsection
<style>
.category-scroll {
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: #ccc transparent;
}

.category-scroll::-webkit-scrollbar {
    height: 8px;
}
.category-scroll::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}
.category-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}
.category-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-4px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
</style>
