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
$mainImage = $primary ? asset('storage/'.$primary->image_path) : ($product->image ? asset('storage/'.$product->image) : asset('assets/client/img/product/product-1.webp'));
@endphp
<img id="mainImage" src="{{ $mainImage }}" class="img-fluid rounded w-100" alt="{{ $product->name }}">
@if($galleries->count())
<div class="d-flex gap-2 mt-3 flex-wrap">
@foreach($galleries as $item)
<img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->alt_text ?? $product->name }}" class="rounded border" style="width: 84px; height: 84px; object-fit: cover; cursor:pointer;" onclick="document.getElementById('mainImage').src='{{ asset('storage/'.$item->image_path) }}'">
@endforeach
</div>
@endif
</div>

<!-- THÔNG TIN SẢN PHẨM -->
<div class="col-lg-6">
<h2 class="fw-bold mb-3 text-capitalize">{{ $product->name }}</h2>
<p class="text-muted">{{ $product->brand ? 'Thương hiệu: ' . ($product->brand->name ?? '') : '' }}</p>

<!-- GIÁ -->
<div class="d-flex align-items-center gap-3 mb-3">
<span class="fs-3 fw-semibold text-primary" id="productPrice"></span>
</div>

<!-- TỒN KHO -->
<div class="mb-3">
<span class="badge p-2 fs-6" id="stockBadge"></span>
</div>

<!-- CHỌN BIẾN THỂ -->
@if($product->variants->count() > 0)
<div class="mb-4">
<label class="form-label fw-semibold mb-2">Chọn biến thể:</label>
@foreach(['size'=>$sizes,'scent'=>$scents,'concentration'=>$concentrations] as $group=>$values)
@if($values->count())
<div class="variant-{{ $group }} mb-2">
<div class="fw-semibold mb-1">{{ ucfirst($group) }}:</div>
@foreach($values as $val)
<button type="button" class="btn btn-outline-secondary btn-sm me-1 mb-1 variant-btn" data-group="{{ $group }}" data-value="{{ $val }}">{{ $val }}</button>
@endforeach
</div>
@endif
@endforeach
<div id="variantInfo" class="mt-2 small text-muted"></div>
</div>
@endif

<!-- FORM GIỎ HÀNG -->
<form id="addToCartForm" method="POST" action="{{ route('cart.add') }}">
@csrf
<input type="hidden" name="product_id" value="{{ $product->id }}">
<input type="hidden" name="variant_id" id="selectedVariantId">
<div class="d-flex align-items-center gap-2 mb-3">
<input type="number" name="quantity" id="productQuantity" value="1" min="1" class="form-control w-auto text-center" style="max-width: 80px;">
<button type="submit" class="btn btn-primary" id="addToCartBtn">
<i class="bi bi-cart-plus"></i> Thêm vào giỏ
</button>
</div>
</form>
</div>
</div>

<!-- MÔ TẢ -->
<div class="mt-5">
<h4 class="mb-3">Mô tả chi tiết</h4>
<p>{{ $product->description ?? 'Chưa có mô tả cho sản phẩm này.' }}</p>
</div>

<!-- ĐÁNH GIÁ -->
<div class="mt-5">
<h4 class="mb-3">Đánh giá</h4>
<div class="mb-3">
<strong>Điểm trung bình:</strong>
<span>{{ number_format($product->average_rating,1) }}/5</span>
<span class="text-muted">({{ $product->reviews_count }} lượt)</span>
</div>
@if(isset($reviews) && $reviews->count())
<div id="reviews-list" class="list-group mb-3">
@include('client.partials.reviews',['reviews'=>$reviews])
</div>
@else
<p class="text-muted">Chưa có đánh giá.</p>
@endif
</div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded',function(){
const variantMatrix=@json($variantMatrix);
const selected={size:null,scent:null,concentration:null};
const variantButtons=document.querySelectorAll('.variant-btn');
const selectedVariantId=document.getElementById('selectedVariantId');
const variantInfo=document.getElementById('variantInfo');
const addToCartBtn=document.getElementById('addToCartBtn');
const quantityInput=document.getElementById('productQuantity');
const stockBadge=document.getElementById('stockBadge');
const productPrice=document.getElementById('productPrice');
const mainImage=document.getElementById('mainImage');

// giá min-max khi chưa chọn đầy đủ
function getPriceRange(filtered=variantMatrix){
    const prices=filtered.filter(v=>v.stock>0).map(v=>v.price).filter(p=>p!=null);
    if(prices.length===0) return 'Liên hệ';
    const min=Math.min(...prices);
    const max=Math.max(...prices);
    return min===max?new Intl.NumberFormat('vi-VN').format(min)+' VNĐ':
           new Intl.NumberFormat('vi-VN').format(min)+' - '+new Intl.NumberFormat('vi-VN').format(max)+' VNĐ';
}

function updateUI(){
    let filtered=variantMatrix.filter(v=>
        (!selected.size||v.size===selected.size)&&
        (!selected.scent||v.scent===selected.scent)&&
        (!selected.concentration||v.concentration===selected.concentration)
    );

    let variant=null;
    if(selected.size&&selected.scent&&selected.concentration){
        variant=filtered.find(v=>v.size===selected.size&&v.scent===selected.scent&&v.concentration===selected.concentration);
    }

    // stock, price, image
    let stock=variant?variant.stock:filtered.reduce((sum,v)=>sum+v.stock,0);
    let price=variant?variant.price:getPriceRange(filtered);
    let image=variant&&variant.image?variant.image:mainImage.src;

    stockBadge.textContent=stock>0?(variant?'Tồn kho: '+stock:'Tồn kho: '+stock):'Hết hàng';
    stockBadge.className='badge '+(stock>0?'bg-success':'bg-danger')+' p-2 fs-6';
    productPrice.textContent=typeof price==='number'?new Intl.NumberFormat('vi-VN').format(price)+' VNĐ':price;
    quantityInput.max=stock;
    addToCartBtn.disabled=stock<=0;
    if(image) mainImage.src=image;

    // trạng thái nút biến thể
    ['size','scent','concentration'].forEach(group=>{
        document.querySelectorAll('.variant-'+group+' button').forEach(btn=>{
            const val=btn.dataset.value;
            const hasStock=variantMatrix.some(v=>
                (!selected.size||v.size===selected.size||group==='size')&&
                (!selected.scent||v.scent===selected.scent||group==='scent')&&
                (!selected.concentration||v.concentration===selected.concentration||group==='concentration')&&
                v[group]===val&&v.stock>0
            );
            btn.disabled=!hasStock;
            btn.classList.toggle('active',selected[group]===val);
        });
    });

    // thông tin
    let info='';
    if(selected.size) info+='Kích thước: '+selected.size+' | ';
    if(selected.scent) info+='Mùi: '+selected.scent+' | ';
    if(selected.concentration) info+='Nồng độ: '+selected.concentration+' | ';
    if(variant) info+='Tồn kho: '+variant.stock;
    variantInfo.textContent=info;

    selectedVariantId.value=variant?variant.id:'';
}

// click chọn/ bỏ chọn
variantButtons.forEach(btn=>{
    btn.addEventListener('click',function(){
        const group=this.dataset.group;
        const value=this.dataset.value;
        if(selected[group]===value) selected[group]=null;
        else selected[group]=value;
        updateUI();
    });
});

// khởi tạo hiển thị ban đầu
updateUI();
});
</script>
@endsection
