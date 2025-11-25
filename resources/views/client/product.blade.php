@extends('client.layouts.app')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('category.show', $product->category->slug) }}">
                        {{ $product->category->category_name }}
                    </a>
                </li>

                <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Sản phẩm' }}</li>
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
                    <img id="mainImage" src="{{ asset('storage/' . $primary->image_path) }}"
                        class="img-fluid rounded w-100" alt="{{ $primary->alt_text ?? $product->name }}">
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
                    <a href="{{ asset('storage/' . $item->image_path) }}" class="glightbox" data-gallery="product"
                        data-large="{{ asset('storage/' . $item->image_path) }}">
                        <img src="{{ asset('storage/' . $item->image_path) }}"
                            alt="{{ $item->alt_text ?? $product->name }}" class="rounded border"
                            style="width: 84px; height: 84px; object-fit: cover;">
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-capitalize">{{ $product->name }}</h2>
                <p class="text-muted">{{ $product->brand ? 'Thương hiệu: ' . $product->brand : '' }}</p>
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->sale_price)
                    <span class="fs-3 fw-semibold text-primary">{{ number_format($product->sale_price, 0, ',', '.') }}
                        VNĐ</span>
                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 0, ',', '.')
                        }} VNĐ</span>
                    @if($product->discount_percentage)
                    <span class="badge bg-danger">-{{ $product->discount_percentage }}%</span>
                    @endif
                    @else
                    <span class="fs-3 fw-semibold text-primary">{{ number_format($product->price, 0, ',', '.') }}
                        VNĐ</span>
                    @endif
                </div>
                <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="d-flex gap-2 mb-4 align-items-center">
                        <label class="form-label mb-0 me-2">Số lượng:</label>
                        <button type="button" class="btn btn-outline-secondary quantity-decrease">-</button>
                        <input type="number" name="quantity" id="productQuantity"
                            class="form-control w-auto text-center" value="1" min="1" max="100"
                            style="max-width: 80px;">
                        <button type="button" class="btn btn-outline-secondary quantity-increase">+</button>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary" id="addToCartBtn">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                        </button>
                        <a href="{{ route('checkout.index') }}" class="btn btn-outline-primary">Mua ngay</a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('compare.add', $product->id) }}" 
                            class="btn btn-outline-primary btn-sm add-to-compare">
                            Thêm vào so sánh
                            </a>
                            <a href="{{ route('compare.index') }}" 
                            class="btn btn-outline-success btn-sm">
                            Xem danh sách
                            </a>
                        </div>
                        <script>
                        document.querySelectorAll('.add-to-compare').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                fetch(this.href)
                                    .then(res => res.text())
                                    .then(() => alert('Đã thêm vào so sánh!'));
                            });
                        });
                        </script>
                    </div>
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
            <div class="list-group mb-4">
                @foreach($reviews as $review)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $review->user->name ?? 'Người dùng' }}</strong>
                            <span class="ms-2">{{ $review->rating }}/5</span>
                        </div>
                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
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
            <form action="{{ route('product.review.store', $product->slug ?? $product->id) }}" method="POST"
                class="border p-3 rounded">
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
                    <textarea id="comment" name="comment" class="form-control" rows="3"
                        placeholder="Viết nhận xét (tuỳ chọn)"></textarea>
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
                            @php $img = $item->primaryImage() ? asset('storage/'.$item->primaryImage()->image_path) :
                            ($item->image ? asset('storage/'.$item->image) :
                            asset('assets/client/img/product/product-1.webp')); @endphp
                            <img src="{{ $img }}" class="card-img-top" alt="{{ $item->name }}">
                            <div class="card-body">
                                <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                <div class="small text-primary">{{ $item->formatted_sale_price ?? $item->formatted_price
                                    }}</div>
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

        // Image gallery
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

        // Add to cart form submission
        const addToCartForm = document.getElementById('addToCartForm');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                const btn = document.getElementById('addToCartBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';
                }
            });
        }
    });
</script>
@endsection
