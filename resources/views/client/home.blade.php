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
                    @if($heroBanner && $heroBanner->image)
                        <img class="img-fluid rounded" src="{{ asset('storage/' . $heroBanner->image) }}" alt="Hero">
                    @else
                        <img class="img-fluid rounded" src="{{ asset('assets/client/img/default-hero.webp') }}" alt="Hero">
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
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-fluid">
                                </div>
                                <h3 class="category-title">{{ $category->name }}</h3>
                                <p class="category-count">{{ $category->products_count ?? $category->products->count() }} Products</p>
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

    <!-- Featured Products -->
    <section class="py-5 border-top">
        <div class="container-fluid container-xl">
            <h3 class="fw-semibold mb-4">Sản phẩm nổi bật</h3>

            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card h-100">

                            <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none">
                                @if($product->primaryImageModel)
                                    <img src="{{ asset('storage/' . $product->primaryImageModel->image_path) }}"
                                         class="card-img-top" style="height:250px; object-fit:cover;">
                                @else
                                    <img src="{{ asset('assets/client/img/product/product-1.webp') }}"
                                         class="card-img-top" style="height:250px; object-fit:cover;">
                                @endif
                            </a>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-1">
                                    <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                <div class="mb-3">
                                    @if($product->sale_price)
                                        <span class="text-primary fw-bold fs-5">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</span>
                                        <span class="text-muted text-decoration-line-through ms-2">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                    @else
                                        <span class="text-primary fw-bold fs-5">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                    @endif
                                </div>

                                <div class="mt-auto d-flex gap-2">
                                    <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-primary flex-fill">Xem chi tiết</a>

                                    <form method="POST" action="{{ route('cart.add') }}" class="d-inline flex-fill">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Best Sellers -->
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

                            <a href="{{ route('product.show', $product->slug) }}">
                                <div class="product-image">
                                    @if($product->primaryImageModel)
                                        <img src="{{ asset('storage/' . $product->primaryImageModel->image_path) }}"
                                             class="img-fluid default-image">
                                    @else
                                        <img src="{{ asset('assets/client/img/product/product-1.webp') }}"
                                             class="img-fluid default-image">
                                    @endif
                                </div>
                            </a>

                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>

                                <div class="product-price">
                                    @if($product->sale_price)
                                        <span class="text-primary fw-bold">{{ number_format($product->sale_price,0,',','.') }} VNĐ</span>
                                        <span class="text-muted text-decoration-line-through ms-2">{{ number_format($product->price,0,',','.') }} VNĐ</span>
                                    @else
                                        <span class="text-primary fw-bold">{{ number_format($product->price,0,',','.') }} VNĐ</span>
                                    @endif
                                </div>

                                <div class="mt-2 d-flex gap-2">
                                    <a href="{{ route('product.show', $product->slug) }}" class="btn btn-sm btn-outline-primary flex-fill">Chi tiết</a>

                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

</main>


@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;">
        {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(() => document.querySelector('.alert-success')?.remove(), 3000);
    </script>
@endif


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count on page load
    loadCartCount();
    
    // Initialize all cart add forms
    initCartForms();
});

// Function to load cart count
function loadCartCount() {
    fetch('{{ route("cart.count") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.count || 0);
        }
    })
    .catch(error => {
        console.error('Error loading cart count:', error);
    });
}

// Function to update cart badge
function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (badge) {
        badge.textContent = count;
        // Animation effect
        badge.style.transition = 'transform 0.2s';
        badge.style.transform = 'scale(1.2)';
        setTimeout(() => {
            badge.style.transform = 'scale(1)';
        }, 200);
    }
}

// Function to show notification
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Initialize all cart add forms to use AJAX
function initCartForms() {
    const cartForms = document.querySelectorAll('form[action*="cart.add"]');
    
    cartForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật badge giỏ hàng
                    updateCartBadge(data.cart_count || 0);
                    // Hiển thị thông báo
                    showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                } else {
                    showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        });
    });
}

// Make functions available globally
window.loadCartCount = loadCartCount;
window.updateCartBadge = updateCartBadge;
window.showNotification = showNotification;
</script>

@endsection
