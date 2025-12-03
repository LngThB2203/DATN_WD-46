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

            <!-- ẢNH SẢN PHẨM -->
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

            <!-- THÔNG TIN SẢN PHẨM -->
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-capitalize">{{ $product->name }}</h2>
                <p class="text-muted">{{ $product->brand ? 'Thương hiệu: ' . ($product->brand->name ?? '') : '' }}</p>

                <!-- GIÁ SẢN PHẨM -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="fs-3 fw-semibold text-primary product-price">
                        {{ $product->formatted_sale_price ?? $product->formatted_price }}
                    </span>
                </div>

                <!-- TỒN KHO (theo controller) -->
                <div class="mb-3">
                    <span class="badge {{ $totalStock > 0 ? 'bg-success' : 'bg-danger' }} p-2 fs-6">
                        {{ $totalStock > 0 ? 'Tồn kho: '.$totalStock.' sản phẩm' : 'Hết hàng' }}
                    </span>
                </div>

                <!-- CHỌN BIẾN THỂ -->
                @if($product->variants->count() > 0)
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">Chọn biến thể:</label>
                        <select name="variant_id" id="variantSelect" class="form-select">
                            <option value="">-- Chọn biến thể --</option>
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}"
                                        data-stock="{{ $variant->stock }}"
                                        data-price="{{ $variant->price ?? ($product->price + ($variant->price_adjustment ?? 0)) }}"
                                        data-size="{{ $variant->size->size_name ?? '' }}"
                                        data-scent="{{ $variant->scent->scent_name ?? '' }}"
                                        data-concentration="{{ $variant->concentration->concentration_name ?? '' }}">
                                    @if($variant->size) Kích thước: {{ $variant->size->size_name }} @endif
                                    @if($variant->scent) | Mùi: {{ $variant->scent->scent_name }} @endif
                                    @if($variant->concentration) | Nồng độ: {{ $variant->concentration->concentration_name }} @endif
                                    - Giá: {{ number_format($variant->price ?? ($product->price + ($variant->price_adjustment ?? 0)), 0, ',', '.') }} VNĐ
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
                    <input type="hidden" name="variant_id" id="selectedVariantId">
                    <input type="number" name="quantity" id="productQuantity" value="1" min="1" class="form-control w-auto text-center mb-3" style="max-width: 80px;">
                    <button type="submit" class="btn btn-primary" id="addToCartBtn" {{ $totalStock <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus"></i> {{ $totalStock > 0 ? 'Thêm vào giỏ' : 'Hết hàng' }}
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-5">
            <h4 class="mb-3">Mô tả chi tiết</h4>
            <p>{{ $product->description ?? 'Chưa có mô tả cho sản phẩm này.' }}</p>
        </div>
        <div class="mt-5">
            <h4 class="mb-3">Đánh giá</h4>
            <div class="mb-3">
                <strong>Điểm trung bình:</strong>
                <span>{{ number_format($product->average_rating, 1) }}/5</span>
                <span class="text-muted">({{ $product->reviews_count }} lượt)</span>
            </div>
            @if(isset($reviews) && $reviews->count())
                <div id="reviews-list" class="list-group mb-3">
                    @include('client.partials.reviews', ['reviews' => $reviews])
                </div>
                <div class="d-grid mb-4">
                    @php
                        $perPage = request('per_page', 5);
                        $nextPage = $reviews->currentPage() + 1;
                        $hasMore = $reviews->hasMorePages();
                    @endphp
                    <button
                        id="load-more-reviews"
                        class="btn btn-outline-secondary"
                        data-next-url="{{ $hasMore ? route('product.reviews.index', $product->slug) . '?page=' . $nextPage . '&per_page=' . $perPage : '' }}"
                        @if(!$hasMore) style="display:none" @endif
                    >Xem thêm</button>
                </div>
            @else
                <p class="text-muted">Chưa có đánh giá.</p>
            @endif

            @auth
                <form action="{{ route('product.review.store', $product->slug ?? $product->id) }}" method="POST" class="border p-3 rounded">
                    @csrf
                    <div class="mb-3">
                        <label for="rating" class="form-label">Chấm điểm (1-5)</label>
                        <select id="rating" name="rating" class="form-select" required>
                            <option value="">Chọn</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Nhận xét</label>
                        <textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Viết nhận xét (tuỳ chọn)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </form>
            @else
                <p class="mt-3">Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đánh giá.</p>
            @endauth
        </div>

        @if(isset($relatedProducts) && $relatedProducts->count())
            <div class="mt-5">
                <h4 class="mb-3">Sản phẩm tương tự</h4>
                <div class="row g-3">
                    @foreach($relatedProducts as $item)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('product.show', $item->slug ?? $item->id) }}" class="text-decoration-none">
                                <div class="card h-100">
                                    @php $img = $item->primaryImage() ? asset('storage/'.$item->primaryImage()->image_path) : ($item->image ? asset('storage/'.$item->image) : asset('assets/client/img/product/product-1.webp')); @endphp
                                    <img src="{{ $img }}" class="card-img-top" alt="{{ $item->name }}">
                                    <div class="card-body">
                                        <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                        <div class="small text-primary">{{ $item->formatted_sale_price ?? $item->formatted_price }}</div>
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
document.addEventListener('DOMContentLoaded', function() {
    const variantSelect = document.getElementById('variantSelect');
    const selectedVariantId = document.getElementById('selectedVariantId');
    const variantInfo = document.getElementById('variantInfo');
    const productPriceSpan = document.querySelector('.product-price');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const quantityInput = document.getElementById('productQuantity');

    if (variantSelect) {
        variantSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if(selectedOption.value){
                selectedVariantId.value = selectedOption.value;

                // Lấy thông tin biến thể
                const stock = selectedOption.dataset.stock;
                const price = selectedOption.dataset.price;
                const size = selectedOption.dataset.size;
                const scent = selectedOption.dataset.scent;
                const concentration = selectedOption.dataset.concentration;

                // Hiển thị thông tin biến thể
                let infoText = '';
                if(size) infoText += 'Kích thước: ' + size + ' | ';
                if(scent) infoText += 'Mùi: ' + scent + ' | ';
                if(concentration) infoText += 'Nồng độ: ' + concentration + ' | ';
                infoText += 'Tồn kho: ' + stock;
                variantInfo.textContent = infoText;

                // Cập nhật giá
                if(productPriceSpan) {
                    productPriceSpan.textContent = new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
                }

                // Cập nhật nút thêm giỏ hàng
                addToCartBtn.disabled = parseInt(stock) <= 0;

                // Cập nhật max số lượng
                if(quantityInput) {
                    quantityInput.setAttribute('max', stock);
                }
            } else {
                selectedVariantId.value = '';
                variantInfo.textContent = '';
                productPriceSpan.textContent = '{{ $product->formatted_sale_price ?? $product->formatted_price }}';
                addToCartBtn.disabled = {{ $totalStock <= 0 ? 'true' : 'false' }};
            }
        });
        var loadMoreBtn = document.getElementById('load-more-reviews');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                var nextUrl = loadMoreBtn.getAttribute('data-next-url');
                if (!nextUrl) {
                    loadMoreBtn.style.display = 'none';
                    return;
                }
                loadMoreBtn.disabled = true;
                loadMoreBtn.textContent = 'Đang tải...';
                fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data && data.html) {
                            var container = document.getElementById('reviews-list');
                            var temp = document.createElement('div');
                            temp.innerHTML = data.html;
                            var items = temp.children;
                            while (items.length) {
                                container.appendChild(items[0]);
                            }
                        }
                        if (data && data.next_page_url) {
                            loadMoreBtn.setAttribute('data-next-url', data.next_page_url);
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.textContent = 'Xem thêm';
                        } else {
                            loadMoreBtn.style.display = 'none';
                        }
                    })
                    .catch(function () {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.textContent = 'Xem thêm';
                    });
            });
        }

        // Quantity controls
        const quantityInput = document.getElementById('productQuantity');
        const decreaseBtn = document.querySelector('.quantity-decrease');
        const increaseBtn = document.querySelector('.quantity-increase');

        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
        }

        if (increaseBtn) {
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const max = parseInt(quantityInput.getAttribute('max')) || 100;
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

        // Add to cart with AJAX (custom handler for product detail page)
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const btn = document.getElementById('addToCartBtn');
                const originalBtnText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';
                
                const formData = new FormData(this);
                
                fetch(this.action, {
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
                        // Cập nhật badge giỏ hàng (sử dụng function từ layout)
                        if (window.updateCartBadge) {
                            window.updateCartBadge(data.cart_count || 0);
                        }
                        
                        // Hiển thị thông báo thành công
                        if (window.showNotification) {
                            window.showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                        }
                        
                        // Reset button
                        btn.disabled = false;
                        btn.innerHTML = originalBtnText;
                    } else {
                        if (window.showNotification) {
                            window.showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                        }
                        btn.disabled = false;
                        btn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (window.showNotification) {
                        window.showNotification('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!', 'error');
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                });
            });
        }
    });
</script>
@endsection
