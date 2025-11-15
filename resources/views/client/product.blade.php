@extends('client.layouts.app')

@section('title', $product->name ?? 'Chi tiết sản phẩm')

@section('content')

{{-- ============================
     BREADCRUMB
============================= --}}
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

{{-- ============================
     PRODUCT DETAIL
============================= --}}
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">

            {{-- ===== LEFT: IMAGE GALLERY ===== --}}
            <div class="col-lg-6">

                @php
                    $primary = $galleries->where('is_primary', true)->first() ?? $galleries->first();
                @endphp

                {{-- Main Image --}}
                @if($primary)
                    <a href="{{ asset('storage/' . $primary->image_path) }}" class="glightbox" data-gallery="product">
                        <img id="mainImage" src="{{ asset('storage/' . $primary->image_path) }}"
                             class="img-fluid rounded w-100" alt="{{ $primary->alt_text ?? $product->name }}">
                    </a>
                @elseif($product->image)
                    <a href="{{ asset('storage/' . $product->image) }}" class="glightbox" data-gallery="product">
                        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}"
                             class="img-fluid rounded w-100" alt="{{ $product->name }}">
                    </a>
                @else
                    <img id="mainImage" src="{{ asset('assets/client/img/product/product-1.webp') }}"
                         class="img-fluid rounded w-100" alt="{{ $product->name }}">
                @endif

                {{-- Thumbnail Images --}}
                @if($galleries->count())
                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        @foreach($galleries as $item)
                            <a href="{{ asset('storage/' . $item->image_path) }}"
                               class="glightbox"
                               data-gallery="product"
                               data-large="{{ asset('storage/' . $item->image_path) }}">
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     class="rounded border"
                                     alt="{{ $item->alt_text ?? $product->name }}"
                                     style="width: 84px; height: 84px; object-fit: cover;">
                            </a>
                        @endforeach
                    </div>
                @endif

            </div>

            {{-- ===== RIGHT: PRODUCT INFO ===== --}}
            <div class="col-lg-6">

                <h2 class="fw-bold mb-3 text-capitalize">{{ $product->name }}</h2>
                <p class="text-muted">{{ $product->brand ? 'Thương hiệu: ' . $product->brand : '' }}</p>

                {{-- Price --}}
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

                {{-- Add to Cart --}}
                <div class="d-flex gap-3">
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="d-flex align-items-center gap-2">

                            <button type="button" id="qty-minus" class="btn btn-outline-secondary px-3">−</button>

                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   value="1"
                                   min="1"
                                   class="form-control text-center"
                                   style="width: 70px;">

                            <button type="button" id="qty-plus" class="btn btn-outline-secondary px-3">+</button>

                        </div>

                        <button class="btn btn-primary mt-2">Thêm vào giỏ</button>
                    </form>
                </div>

                <button class="btn btn-primary mt-2">Mua Ngay</button>

            </div>
        </div>

        {{-- ========= DESCRIPTION ========= --}}
        <div class="mt-5">
            <h4 class="mb-3">Mô tả chi tiết</h4>
            <p>{{ $product->description }}</p>
        </div>

        {{-- ========= REVIEWS ========= --}}
        <div class="mt-5">
            <h4 class="mb-3">Đánh giá</h4>

            <div class="mb-3">
                <strong>Điểm trung bình:</strong>
                <span>{{ number_format($product->average_rating, 1) }}/5</span>
                <span class="text-muted">({{ $product->reviews_count }} lượt)</span>
            </div>

            {{-- Review List --}}
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

            {{-- Review form --}}
            @auth
                <form action="{{ route('product.review.store', $product->slug) }}"
                      method="POST"
                      class="border p-3 rounded">
                    @csrf

                    <div class="mb-3">
                        <label for="rating" class="form-label">Chấm điểm (1-5)</label>
                        <select id="rating" name="rating" class="form-select" required>
                            <option value="">Chọn</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
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

        {{-- ========= RELATED PRODUCTS ========= --}}
        @if(isset($relatedProducts) && $relatedProducts->count())
            <div class="mt-5">
                <h4 class="mb-3">Sản phẩm tương tự</h4>

                <div class="row g-3">
                    @foreach($relatedProducts as $item)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('product.show', $item->slug) }}" class="text-decoration-none">

                                <div class="card h-100">
                                    @php
                                        $img = $item->primaryImage()
                                                ? asset('storage/'.$item->primaryImage()->image_path)
                                                : ($item->image
                                                    ? asset('storage/'.$item->image)
                                                    : asset('assets/client/img/product/product-1.webp')
                                                  );
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

{{-- ============================
     SCRIPT — GỘP LẠI CHO GỌN
============================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ==== 1. BUTTON + / - SỐ LƯỢNG ==== */
    const minusBtn = document.getElementById('qty-minus');
    const plusBtn = document.getElementById('qty-plus');
    const qtyInput = document.getElementById('quantity');

    if (minusBtn && plusBtn && qtyInput) {

        minusBtn.addEventListener('click', () => {
            const value = parseInt(qtyInput.value);
            if (value > 1) qtyInput.value = value - 1;
        });

        plusBtn.addEventListener('click', () => {
            const value = parseInt(qtyInput.value);
            qtyInput.value = value + 1;
        });
    }

    /* ==== 2. LIGHTBOX ==== */
    if (window.GLightbox) {
        GLightbox({ selector: '.glightbox' });
    }

    /* ==== 3. CLICK ẢNH THUMB → ĐỔI ẢNH CHÍNH ==== */
    const mainImage = document.getElementById('mainImage');
    const thumbs = document.querySelectorAll('[data-gallery="product"][data-large]');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function (e) {
            e.preventDefault();

            const large = this.getAttribute('data-large');
            const wrapper = mainImage.closest('a.glightbox');

            if (wrapper) wrapper.setAttribute('href', large);

            mainImage.setAttribute('src', large);
        });
    });

});
</script>

@endsection
