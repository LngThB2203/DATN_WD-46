@extends('client.layouts.client')

@section('title', 'Trang chủ')

@section('content')
<main class="main">

    <!-- Hero Section -->
    <section class="hero py-1">
        <div class="container-fluid container-xl">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-3">Cửa hàng nước hoa trực tuyến</h2>
                    <p class="lead mb-4">Khám phá bộ sưu tập nước hoa chính hãng với ưu đãi hấp dẫn. Giao nhanh, đổi trả
                        dễ dàng.</p>
                    <a href="#" class="btn btn-primary btn-lg">Mua ngay</a>
                </div>
                <div class="col-lg-6">
                    @if(isset($heroBanners) && $heroBanners->count() > 0)
                    @if($heroBanners->count() == 1)
                    @php $product = $products->first(); @endphp
                    @if($product)
                    <a href="{{ route('product.show', $product->slug) }}">
                        <img src="{{ asset('storage/' . $heroBanners->first()->image) }}" class="d-block w-100 rounded"
                            alt="Hero Banner">
                    </a>
                    @endif
                    @else
                    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($heroBanners as $key => $banner)
                            <div class="carousel-item @if($key == 0) active @endif">
                                <a
                                    href="{{ isset($products) && $products->count() > 0 ? route('product.show', $products->first()->slug) : '#' }}">
                                    <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100 rounded"
                                        alt="Hero Banner">
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden"></span>
                        </button>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        </div>
    </section>

    <!-- Best Selling Products -->
    <section class="py-5 border-top">
        <div class="container-fluid container-xl">
            <h3 class="fw-semibold mb-4">Sản phẩm bán chạy</h3>

            <div class="row g-4" id="bestSellingList">
                @forelse($bestSellingProducts as $product)
                @php
                $img = $product->galleries->where('is_primary', true)->first() ?? $product->galleries->first();
                $imgUrl = $img ? asset('storage/'.$img->image_path) : asset('assets/client/img/product/product-1.webp');
                @endphp

                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="card h-100 position-relative">
                        <img src="{{ $imgUrl }}" class="card-img-top"
                            style="height:250px; object-fit:cover; border:1px solid #dee2e6; border-radius:4px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">{{ $product->name }}</h5>

                            @if($product->variants->count())
                            <div class="variant-popup border p-3 bg-white shadow position-absolute top-50 start-50 translate-middle d-none"
                                style="z-index:10; width:95%; max-width:300px; max-height:500px; overflow-y:auto;">
                                <h6 class="mb-3">Chọn biến thể</h6>

                                <!-- Nút Tất cả sản phẩm -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 variant-all-btn"
                                        data-product-id="{{ $product->id }}">
                                        Tất cả sản phẩm
                                    </button>
                                </div>

                                <!-- Danh sách từng biến thể -->
                                <div class="variant-list">
                                    @foreach($product->variants as $variant)
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-2 variant-option-btn"
                                        data-variant-id="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                        data-stock="{{ $variant->total_stock }}" data-product-id="{{ $product->id }}">
                                        {{ $variant->size->size_name ?? 'N/A' }} - {{ $variant->scent->scent_name ??
                                        'N/A' }}
                                    </button>
                                    @endforeach
                                </div>

                                <button class="btn btn-primary w-100 mb-2 confirm-add-btn"
                                    data-product-id="{{ $product->id }}">
                                    <i class="bi bi-cart3"></i> Thêm vào giỏ
                                </button>
                                <button class="btn btn-outline-secondary w-100 close-popup-btn">Hủy</button>
                            </div>
                            @endif

                            <div class="mt-auto">
                                <div class="product-price mb-2">
                                    <span class="text-primary fw-bold">{{ number_format($product->price, 0, ',', '.') }}
                                        VNĐ</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-secondary btn-sm add-to-cart-btn"
                                        data-product-id="{{ $product->id }}">
                                        <i class="bi bi-cart3"></i>
                                    </button>
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-center text-muted">Chưa có sản phẩm bán chạy.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Latest Products -->
    <section class="py-5 border-top">
        <div class="container-fluid container-xl">
            <h3 class="fw-semibold mb-4">Sản phẩm mới nhất</h3>

            <div class="row g-4" id="productList">
                @foreach($products as $product)
                @php
                $img = $product->galleries->where('is_primary', true)->first() ?? $product->galleries->first();
                $imgUrl = $img ? asset('storage/'.$img->image_path) : asset('assets/client/img/product/product-1.webp');
                @endphp

                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="card h-100 position-relative">
                        <img src="{{ $imgUrl }}" class="card-img-top"
                            style="height:250px; object-fit:cover; border:1px solid #dee2e6; border-radius:4px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">{{ $product->name }}</h5>

                            @if($product->variants->count())
                            <div class="variant-popup border p-3 bg-white shadow position-absolute top-50 start-50 translate-middle d-none"
                                style="z-index:10; width:95%; max-width:300px; max-height:500px; overflow-y:auto;">
                                <h6 class="mb-3">Chọn biến thể</h6>

                                <!-- Nút Tất cả sản phẩm -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 variant-all-btn"
                                        data-product-id="{{ $product->id }}">
                                        Tất cả sản phẩm
                                    </button>
                                </div>

                                <!-- Danh sách từng biến thể -->
                                <div class="variant-list">
                                    @foreach($product->variants as $variant)
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-2 variant-option-btn"
                                        data-variant-id="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                        data-stock="{{ $variant->total_stock }}" data-product-id="{{ $product->id }}">
                                        {{ $variant->size->size_name ?? 'N/A' }} - {{ $variant->scent->scent_name ??
                                        'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ number_format($variant->price, 0, ',', '.') }}
                                            VNĐ</small>
                                    </button>
                                    @endforeach
                                </div>

                                <button class="btn btn-primary w-100 mb-2 confirm-add-btn"
                                    data-product-id="{{ $product->id }}">
                                    <i class="bi bi-cart3"></i> Thêm vào giỏ
                                </button>
                                <button class="btn btn-outline-secondary w-100 close-popup-btn">Hủy</button>
                            </div>
                            @endif

                            <div class="mt-auto">
                                <div class="product-price mb-2">
                                    <span class="text-primary fw-bold">{{ number_format($product->price, 0, ',', '.') }}
                                        VNĐ</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-secondary btn-sm add-to-cart-btn"
                                        data-product-id="{{ $product->id }}">
                                        <i class="bi bi-cart3"></i>
                                    </button>
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function(){

    // Hiển thị popup biến thể
    document.querySelectorAll('.add-to-cart-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const popup = this.closest('.card-body').querySelector('.variant-popup');
            if(popup){
                popup.classList.remove('d-none');
            } else {
                ajaxAddToCart(productId, null);
            }
        });
    });

    // Đóng popup
    document.querySelectorAll('.close-popup-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const popup = this.closest('.variant-popup');
            popup.classList.add('d-none');
            // Reset selected variant
            popup.querySelectorAll('.variant-option-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            popup.querySelector('.variant-all-btn').classList.remove('btn-primary');
            popup.querySelector('.variant-all-btn').classList.add('btn-outline-primary');
        });
    });

    // Chọn "Tất cả sản phẩm"
    document.querySelectorAll('.variant-all-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const popup = this.closest('.variant-popup');

            // Toggle nút
            this.classList.toggle('btn-primary');
            this.classList.toggle('btn-outline-primary');

            // Reset các biến thể con
            popup.querySelectorAll('.variant-option-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
        });
    });

    // Chọn biến thể
    document.querySelectorAll('.variant-option-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const popup = this.closest('.variant-popup');

            // Toggle nút được chọn
            if(this.classList.contains('btn-primary')){
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-secondary');
            } else {
                // Reset nút "Tất cả sản phẩm"
                popup.querySelector('.variant-all-btn').classList.remove('btn-primary');
                popup.querySelector('.variant-all-btn').classList.add('btn-outline-primary');

                // Reset các nút khác
                popup.querySelectorAll('.variant-option-btn').forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-secondary');
                });

                // Active nút hiện tại
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-secondary');
            }
        });
    });

    // Thêm vào giỏ từ popup
    document.querySelectorAll('.confirm-add-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const popup = this.closest('.variant-popup');

            // Kiểm tra xem có chọn gì không
            const selectedVariants = Array.from(popup.querySelectorAll('.variant-option-btn.btn-primary'));
            const selectedAll = popup.querySelector('.variant-all-btn').classList.contains('btn-primary');

            if(selectedVariants.length === 0 && !selectedAll){
                alert('Vui lòng chọn biến thể trước khi thêm vào giỏ');
                return;
            }

            // Nếu chọn "Tất cả sản phẩm"
            if(selectedAll){
                alert('Vui lòng chọn biến thể cụ thể, không thể thêm tất cả sản phẩm');
                return;
            }

            // Thêm từng biến thể đã chọn
            selectedVariants.forEach(variantBtn => {
                const variantId = variantBtn.dataset.variantId;
                ajaxAddToCart(productId, variantId);
            });

            popup.classList.add('d-none');
            // Reset
            popup.querySelectorAll('.variant-option-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
        });
    });

    // Hàm AJAX add to cart
    function ajaxAddToCart(productId, variantId){
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':'application/json'
            },
            body: new URLSearchParams({
                product_id: productId,
                variant_id: variantId,
                quantity: 1
            })
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                showNotification(data.message,'success');
                updateCartBadge(data.cart_count);
            }else{
                showNotification(data.message,'error');
            }
        });
    }

    // Notification
    function showNotification(msg,type){
        const alertClass = type=='success'?'alert-success':'alert-danger';
        const div = document.createElement('div');
        div.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        div.style.cssText = 'top:20px; right:20px; z-index:9999; min-width:300px';
        div.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(div);
        setTimeout(()=>{div.remove()},3000);
    }

    function updateCartBadge(count){
        const badge = document.getElementById('cartBadge');
        if(badge){
            badge.textContent = count;
            badge.style.transition='transform 0.2s';
            badge.style.transform='scale(1.2)';
            setTimeout(()=>{badge.style.transform='scale(1)'},200);
        }
    }

});
</script>
@endsection
<style>
    #heroCarousel .carousel-item {
        height: 400px;
        overflow: hidden;
    }

    #heroCarousel .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
