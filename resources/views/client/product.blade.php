@extends('client.layouts.app')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('category.show', $product->category->slug ?? $product->category->id) }}">
                            {{ $product->category->category_name }}
                        </a>
                    </li>
                @endif

                <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Sản phẩm' }}</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">

            <!-- ================== ẢNH SẢN PHẨM ================== -->
            <div class="col-lg-6">
                @php
                    $primary = $galleries->where('is_primary', true)->first() ?? $galleries->first();
                @endphp

                @if($primary)
                    <a href="{{ asset('storage/' . $primary->image_path) }}" class="glightbox" data-gallery="product">
                        <img id="mainImage" src="{{ asset('storage/' . $primary->image_path) }}"
                             class="img-fluid rounded w-100"
                             alt="{{ $primary->alt_text ?? $product->name }}">
                    </a>
                @elseif($product->image)
                    <a href="{{ asset('storage/' . $product->image) }}" class="glightbox" data-gallery="product">
                        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded w-100"
                             alt="{{ $product->name }}">
                    </a>
                @else
                    <img id="mainImage" src="{{ asset('assets/client/img/product/product-1.webp') }}"
                         class="img-fluid rounded w-100" alt="{{ $product->name }}">
                @endif

                @if($galleries->count())
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        @foreach($galleries as $item)
                            <a href="{{ asset('storage/' . $item->image_path) }}" class="glightbox"
                               data-gallery="product"
                               data-large="{{ asset('storage/' . $item->image_path) }}">
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     alt="{{ $item->alt_text ?? $product->name }}"
                                     class="rounded border"
                                     style="width: 84px; height: 84px; object-fit: cover;">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ================== THÔNG TIN SẢN PHẨM ================== -->
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-capitalize">{{ $product->name }}</h2>

                <p class="text-muted">
                    {{ $product->brand ? 'Thương hiệu: ' . $product->brand : '' }}
                </p>

                <!-- ======= GIÁ ======= -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->sale_price)
                        <span class="fs-3 fw-semibold text-primary">
                            {{ number_format($product->sale_price, 0, ',', '.') }} VNĐ
                        </span>
                        <span class="text-decoration-line-through text-muted">
                            {{ number_format($product->price, 0, ',', '.') }} VNĐ
                        </span>

                        @if($product->discount_percentage > 0)
                            <span class="badge bg-danger">-{{ $product->discount_percentage }}%</span>
                        @endif

                    @else
                        <span class="fs-3 fw-semibold text-primary">
                            {{ number_format($product->price, 0, ',', '.') }} VNĐ
                        </span>
                    @endif
                </div>

                <!-- ⭐ HIỂN THỊ TỒN KHO -->
                <div class="mb-3">
                    @php
                        $totalStock = $product->stock_quantity;
                        if ($product->variants->count() > 0) {
                            $totalStock += $product->variants->sum('stock');
                        }
                    @endphp
                    @if($totalStock > 0)
                        <span class="badge bg-success p-2 fs-6">
                            Tồn kho: {{ $totalStock }} sản phẩm
                        </span>
                    @else
                        <span class="badge bg-danger p-2 fs-6">
                            Hết hàng
                        </span>
                    @endif
                </div>

                <!-- ⭐ HIỂN THỊ BIẾN THỂ -->
                @if($product->variants->count() > 0)
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">Chọn biến thể:</label>
                        <select name="variant_id" id="variantSelect" class="form-select">
                            <option value="">-- Chọn biến thể --</option>
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}" 
                                        data-stock="{{ $variant->stock }}"
                                        data-price-adjustment="{{ $variant->price_adjustment ?? 0 }}"
                                        data-size="{{ $variant->size->size_name ?? '' }}"
                                        data-scent="{{ $variant->scent->scent_name ?? '' }}"
                                        data-concentration="{{ $variant->concentration->concentration_name ?? '' }}">
                                    @if($variant->size)
                                        Kích thước: {{ $variant->size->size_name }}
                                    @endif
                                    @if($variant->scent)
                                        | Mùi hương: {{ $variant->scent->scent_name }}
                                    @endif
                                    @if($variant->concentration)
                                        | Nồng độ: {{ $variant->concentration->concentration_name }}
                                    @endif
                                    @if($variant->price_adjustment)
                                        ({{ $variant->price_adjustment > 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 0, ',', '.') }} VNĐ)
                                    @endif
                                    - Tồn: {{ $variant->stock }}
                                </option>
                            @endforeach
                        </select>
                        <div id="variantInfo" class="mt-2 small text-muted"></div>
                    </div>
                @endif

                <!-- FORM THÊM GIỎ HÀNG -->
                <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="selectedVariantId" value="">

                    <div class="d-flex gap-2 mb-4 align-items-center">
                        <label class="form-label mb-0 me-2">Số lượng:</label>
                        <button type="button" class="btn btn-outline-secondary quantity-decrease">-</button>

                        <input type="number" name="quantity" id="productQuantity"
                               class="form-control w-auto text-center"
                               value="1" min="1" max="100"
                               style="max-width: 80px;">

                        <button type="button" class="btn btn-outline-secondary quantity-increase">+</button>
                    </div>

                    <div class="d-flex gap-3">
                        @php
                            $totalStock = $product->stock_quantity;
                            if ($product->variants->count() > 0) {
                                $totalStock += $product->variants->sum('stock');
                            }
                            $isOutOfStock = $totalStock <= 0;
                        @endphp
                        <button type="submit" class="btn btn-primary" id="addToCartBtn" {{ $isOutOfStock ? 'disabled' : '' }}>
                            <i class="bi bi-cart-plus"></i> {{ $isOutOfStock ? 'Hết hàng' : 'Thêm vào giỏ' }}
                        </button>

                        <a href="{{ route('checkout.index') }}" class="btn btn-outline-primary" {{ $isOutOfStock ? 'onclick="return false;" style="pointer-events: none; opacity: 0.5;"' : '' }}>
                            Mua ngay
                        </a>
                    </div>
                    @if($isOutOfStock)
                        <div class="alert alert-warning mt-2 mb-0">
                            <small>⚠️ Sản phẩm hiện đang hết hàng. Vui lòng quay lại sau.</small>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- ================== MÔ TẢ ================== -->
        <div class="mt-5">
            <h4 class="mb-3">Mô tả chi tiết</h4>
            <p>{{ $product->description ?? 'Chưa có mô tả cho sản phẩm này.' }}</p>
        </div>

        <!-- ================== ĐÁNH GIÁ ================== -->
        <div class="mt-5">
            <h4 class="mb-3">Đánh giá</h4>

            <div class="mb-3">
                <strong>Điểm trung bình:</strong>
                <span>{{ number_format($product->average_rating, 1) }}/5</span>
                <span class="text-muted">({{ $product->reviews_count }} lượt)</span>
            </div>

            @if(isset($reviews) && $reviews->count())
                <div class="list-group mb-4">
                    @foreach($reviews as $review)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $review->user->name ?? 'Người dùng' }}</strong>
                                    <span class="ms-2">{{ $review->rating }}/5</span>
                                </div>
                                <small class="text-muted">
                                    {{ $review->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>

                            @if($review->comment)
                                <div class="mt-2">{{ $review->comment }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Chưa có đánh giá.</p>
            @endif

            @auth
                <form action="{{ route('product.review.store', $product->slug ?? $product->id) }}"
                      method="POST" class="border p-3 rounded">
                    @csrf

                    <div class="mb-3">
                        <label for="rating" class="form-label">Chấm điểm (1-5)</label>
                        <select id="rating" name="rating" class="form-select" required>
                            <option value="">Chọn</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">Nhận xét</label>
                        <textarea id="comment" name="comment" class="form-control"
                                  rows="3" placeholder="Viết nhận xét (tuỳ chọn)"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </form>
            @else
                <p class="mt-3">
                    Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đánh giá.
                </p>
            @endauth
        </div>

        <!-- ================== SẢN PHẨM TƯƠNG TỰ ================== -->
        @if(isset($relatedProducts) && $relatedProducts->count())
            <div class="mt-5">
                <h4 class="mb-3">Sản phẩm tương tự</h4>
                <div class="row g-3">

                    @foreach($relatedProducts as $item)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('product.show', $item->slug ?? $item->id) }}"
                               class="text-decoration-none">
                                <div class="card h-100">

                                    @php
                                        $img = $item->primaryImage()
                                            ? asset('storage/'.$item->primaryImage()->image_path)
                                            : ($item->image
                                                ? asset('storage/'.$item->image)
                                                : asset('assets/client/img/product/product-1.webp'));
                                    @endphp

                                    <img src="{{ $img }}" class="card-img-top" alt="{{ $item->name }}">

                                    <div class="card-body">
                                        <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                        <div class="small text-primary">
                                            {{ $item->formatted_sale_price ?? $item->formatted_price }}
                                        </div>
                                    </div>

                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>
            </div>
        @endif

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // GLightbox
        if (window.GLightbox) {
            GLightbox({ selector: '.glightbox' });
        }

        // Image gallery switching
        var mainImage = document.getElementById('mainImage');
        var thumbs = document.querySelectorAll('[data-gallery="product"][data-large]');

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function (e) {
                e.preventDefault();
                var large = this.getAttribute('data-large');

                var wrapper = mainImage.closest('a.glightbox');
                if (wrapper) wrapper.setAttribute('href', large);

                mainImage.setAttribute('src', large);
            });
        });

        // Quantity controls
        const quantityInput = document.getElementById('productQuantity');
        const decreaseBtn = document.querySelector('.quantity-decrease');
        const increaseBtn = document.querySelector('.quantity-increase');

        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
        }

        if (increaseBtn) {
            increaseBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                let max = parseInt(quantityInput.getAttribute('max')) || 100;
                if (currentValue < max) {
                    quantityInput.value = currentValue + 1;
                }
            });
        }

        // Variant selection handler
        const variantSelect = document.getElementById('variantSelect');
        const selectedVariantId = document.getElementById('selectedVariantId');
        const variantInfo = document.getElementById('variantInfo');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const buyNowBtn = document.querySelector('a[href*="checkout"]');
        
        function updateStockStatus(stock) {
            const isOutOfStock = parseInt(stock) <= 0;
            if (addToCartBtn) {
                addToCartBtn.disabled = isOutOfStock;
                addToCartBtn.innerHTML = isOutOfStock ? '<i class="bi bi-cart-x"></i> Hết hàng' : '<i class="bi bi-cart-plus"></i> Thêm vào giỏ';
            }
            if (buyNowBtn) {
                if (isOutOfStock) {
                    buyNowBtn.style.pointerEvents = 'none';
                    buyNowBtn.style.opacity = '0.5';
                } else {
                    buyNowBtn.style.pointerEvents = 'auto';
                    buyNowBtn.style.opacity = '1';
                }
            }
        }
        
        if (variantSelect) {
            variantSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    selectedVariantId.value = selectedOption.value;
                    const stock = selectedOption.dataset.stock;
                    const size = selectedOption.dataset.size;
                    const scent = selectedOption.dataset.scent;
                    const concentration = selectedOption.dataset.concentration;
                    
                    let infoText = '';
                    if (size) infoText += 'Kích thước: ' + size + ' | ';
                    if (scent) infoText += 'Mùi: ' + scent + ' | ';
                    if (concentration) infoText += 'Nồng độ: ' + concentration + ' | ';
                    infoText += 'Tồn kho: ' + stock;
                    
                    if (variantInfo) {
                        variantInfo.textContent = infoText;
                    }
                    
                    // Update max quantity based on stock
                    const quantityInput = document.getElementById('productQuantity');
                    if (quantityInput) {
                        quantityInput.setAttribute('max', stock);
                    }
                    
                    // Update button status
                    updateStockStatus(stock);
                } else {
                    selectedVariantId.value = '';
                    if (variantInfo) {
                        variantInfo.textContent = '';
                    }
                    // Reset to product stock
                    @php
                        $totalStock = $product->stock_quantity;
                        if ($product->variants->count() > 0) {
                            $totalStock += $product->variants->sum('stock');
                        }
                    @endphp
                    updateStockStatus({{ $totalStock }});
                }
            });
        }

        // Add to cart loading button
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function() {
                const btn = document.getElementById('addToCartBtn');
                btn.disabled = true;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';
            });
        }

    });
</script>

@endsection
