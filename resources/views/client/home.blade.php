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
                    @if(isset($heroBanners) && $heroBanners->count() > 0)
                    @if($heroBanners->count() == 1)
                    @foreach($products as $product)
                        <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="d-block">
                            <img src="{{ asset('storage/' . $heroBanners->first()->image) }}" class="img-fluid rounded" alt="Hero Banner">
                        </a>
                        @endforeach
                    @else
                        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($heroBanners as $key => $banner)
                                    <div class="carousel-item @if($key == 0) active @endif">
                                        @foreach($products as $product)
                                        <a href="{{  route('product.show', $product->slug)}}" target="_blank" class="d-block">
                                            <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100 rounded" alt="Hero Banner">
                                        </a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden"></span>
                            </button>
                        </div>
                    @endif
                @endif
                </div>
            </div>
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
        });
    });

    // Thêm vào giỏ từ popup
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

    // Cập nhật giá khi chọn biến thể
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
    height: 400px; /* chiều cao cố định cho carousel */
    overflow: hidden;
}

#heroCarousel .carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* ảnh sẽ tự dãn và cắt để lấp đầy khung */
}

</style>
