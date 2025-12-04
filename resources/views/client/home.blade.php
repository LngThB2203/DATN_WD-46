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
                    <p class="lead mb-4">Khám phá bộ sưu tập nước hoa chính hãng với ưu đãi hấp dẫn. Giao nhanh, đổi trả
                        dễ dàng.</p>
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

    <!-- Featured Products -->
    <section class="py-5 border-top">
        <div class="container-fluid container-xl">
            <h3 class="fw-semibold mb-4">Sản phẩm nổi bật</h3>

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

                            <div class="product-price mb-2" id="price-{{ $product->id }}">
                                @if($product->sale_price)
                                <span class="text-primary fw-bold">{{ number_format($product->sale_price,0,',','.') }}
                                    VNĐ</span>
                                <span class="text-muted text-decoration-line-through ms-2">{{
                                    number_format($product->price,0,',','.') }} VNĐ</span>
                                @else
                                <span class="text-primary fw-bold">{{ number_format($product->price,0,',','.') }}
                                    VNĐ</span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <button class="btn btn-outline-secondary btn-sm add-to-cart-btn"
                                    data-product-id="{{ $product->id }}">
                                    <i class="bi bi-cart3"></i>
                                </button>
                                <a href="{{ route('product.show', $product->slug) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    Xem chi tiết
                                </a>
                            </div>

                            <!-- Variant Popup -->
                            @if($product->variants->count())
                            <div class="variant-popup border p-2 bg-white shadow position-absolute top-50 start-50 translate-middle d-none"
                                style="z-index:10; width:90%; max-width:250px;">
                                <select class="form-select variant-select mb-2" data-product-id="{{ $product->id }}">
                                    <option value="">Chọn biến thể</option>
                                    @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price }}">
                                        {{ $variant->size->size_name ?? '' }} {{ $variant->scent->scent_name ?? '' }}
                                    </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary w-100 mb-2 confirm-add-btn"
                                    data-product-id="{{ $product->id }}">
                                    <i class="bi bi-cart3"></i> Thêm vào giỏ
                                </button>
                                <button class="btn btn-secondary w-100 close-popup-btn"
                                    data-product-id="{{ $product->id }}">Hủy</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function(){

    // Hàm hiển thị popup biến thể
    document.querySelectorAll('.add-to-cart-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const popup = this.closest('.card-body').querySelector('.variant-popup');
            if(popup){
                popup.classList.remove('d-none');
            } else {
                // Không có biến thể, AJAX add trực tiếp
                ajaxAddToCart(productId, null);
            }
        });
    });

    // Đóng popup
    document.querySelectorAll('.close-popup-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const popup = this.closest('.variant-popup');
            popup.classList.add('d-none');
        });
    });

    // Xác nhận thêm vào giỏ từ popup
    document.querySelectorAll('.confirm-add-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const select = this.closest('.variant-popup').querySelector('.variant-select');
            const variantId = select.value;
            if(!variantId){
                alert('Vui lòng chọn biến thể trước khi thêm vào giỏ');
                return;
            }
            ajaxAddToCart(productId, variantId);
            this.closest('.variant-popup').classList.add('d-none');
        });
    });

    // Khi chọn biến thể, cập nhật giá
    document.querySelectorAll('.variant-select').forEach(select=>{
        select.addEventListener('change', function(){
            const variantPrice = this.selectedOptions[0]?.dataset?.price ?? null;
            const productId = this.dataset.productId;
            const priceDiv = document.getElementById('price-'+productId);
            if(variantPrice){
                priceDiv.innerHTML = '<span class="text-primary fw-bold">'+parseInt(variantPrice).toLocaleString('vi-VN')+' VNĐ</span>';
            }
        });
    });

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

    // Notification và badge
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
