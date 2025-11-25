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
                <p class="text-muted">{{ $product->brand ? 'Thương hiệu: ' . $product->brand : '' }}</p>

                <!-- GIÁ SẢN PHẨM -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="fs-3 fw-semibold text-primary product-price">
                        {{ $product->formatted_sale_price ?? $product->formatted_price }}
                    </span>
                </div>

                <!-- TỒN KHO -->
                @php
                    $totalStock = $product->stock_quantity;
                    if ($product->variants->count() > 0) {
                        $totalStock += $product->variants->sum('stock');
                    }
                @endphp
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
    }
});
</script>
@endsection
