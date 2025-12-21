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


                   {{-- SIZE --}}
                   @if($sizes->count())
                   <div class="mb-3">
                       <label class="fw-semibold mb-2 d-block">Kích thước</label>
                       <div class="d-flex flex-wrap gap-2 variant-group" data-type="size">
                           @foreach($sizes as $size)
                           <button type="button" class="btn btn-outline-secondary btn-sm variant-option"
                               data-value="{{ $size }}">
                               {{ $size }}
                           </button>
                           @endforeach
                       </div>
                   </div>
                   @endif


                   {{-- SCENT --}}
                   @if($scents->count())
                   <div class="mb-3">
                       <label class="fw-semibold mb-2 d-block">Mùi hương</label>
                       <div class="d-flex flex-wrap gap-2 variant-group" data-type="scent">
                           @foreach($scents as $scent)
                           <button type="button" class="btn btn-outline-secondary btn-sm variant-option"
                               data-value="{{ $scent }}">
                               {{ $scent }}
                           </button>
                           @endforeach
                       </div>
                   </div>
                   @endif


                   {{-- CONCENTRATION --}}
                   @if($concentrations->count())
                   <div class="mb-3">
                       <label class="fw-semibold mb-2 d-block">Nồng độ</label>
                       <div class="d-flex flex-wrap gap-2 variant-group" data-type="concentration">
                           @foreach($concentrations as $c)
                           <button type="button" class="btn btn-outline-secondary btn-sm variant-option"
                               data-value="{{ $c }}">
                               {{ $c }}
                           </button>
                           @endforeach
                       </div>
                   </div>
                   @endif


                   <div id="variantInfo" class="small text-muted"></div>
               </div>
               @endif




               <div class="d-flex align-items-center gap-2">
                   <!-- FORM THÊM GIỎ HÀNG -->
                   <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}" class="mb-0">
                       @csrf
                       <input type="hidden" name="product_id" value="{{ $product->id }}">
                       <input type="hidden" name="variant_id" id="selectedVariantId">


                       <div class="d-flex align-items-center gap-3 mb-3">
                           <div class="input-group" style="width: 140px;">
                               <button class="btn btn-outline-secondary quantity-decrease" type="button">-</button>
                               <input type="number" name="quantity" id="productQuantity" value="1" min="1"
                                   class="form-control text-center">
                               <button class="btn btn-outline-secondary quantity-increase" type="button">+</button>
                           </div>
                           <div class="text-muted small" id="availableStockInfo">
                               {{ $totalStock > 0 ? 'Có sẵn: '.$totalStock.' sản phẩm' : 'Không còn hàng' }}
                           </div>
                       </div>


                       <button type="submit" class="btn btn-primary" id="addToCartBtn" {{ $totalStock <=0 ? 'disabled'
                           : '' }}>
                           <i class="bi bi-cart-plus"></i> {{ $totalStock > 0 ? 'Thêm vào giỏ' : 'Hết hàng' }}
                       </button>
                   </form>


                   @auth
                   <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="mb-0">
                       @csrf
                       <button type="submit" class="btn {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }}">
                           <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                           {{ $isFavorite ? 'Bỏ yêu thích' : 'Yêu thích' }}
                       </button>
                   </form>
                   @endauth
               </div>
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
               <button id="load-more-reviews" class="btn btn-outline-secondary"
                   data-next-url="{{ $hasMore ? route('product.reviews.index', $product->slug) . '?page=' . $nextPage . '&per_page=' . $perPage : '' }}"
                   @if(!$hasMore) style="display:none" @endif>Xem thêm</button>
           </div>
           @else
           <p class="text-muted">Chưa có đánh giá.</p>
           @endif
           <p class="mt-3 text-muted">
               Bạn có thể đánh giá sản phẩm trong mục <strong>Đơn hàng của tôi</strong> sau khi đơn hàng được hoàn thành (trong vòng 15 ngày).
           </p>
       </div>


       @if(isset($relatedProducts) && $relatedProducts->count())
       <div class="mt-5">
           <h4 class="mb-3">Sản phẩm tương tự</h4>
           <div class="row g-3">
               @foreach($relatedProducts as $item)
               <div class="col-6 col-md-4 col-lg-3">
                   <a href="{{ route('product.show', $item->slug ?? $item->id) }}" class="text-decoration-none">
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

   const variantMatrix = @json($variantMatrix);
   const defaultTotalStock = @json($totalStock);
   const defaultAvailableText = @json(
       $totalStock > 0
           ? 'Có sẵn: {{ $totalStock }} sản phẩm'
           : 'Không còn hàng'
   );

   const selectedVariantId = document.getElementById('selectedVariantId');
   const variantInfo = document.getElementById('variantInfo');
   const productPriceSpan = document.querySelector('.product-price');
   const addToCartBtn = document.getElementById('addToCartBtn');
   const quantityInput = document.getElementById('productQuantity');
   const availableStockInfo = document.getElementById('availableStockInfo');

   const selected = {
       size: null,
       scent: null,
       concentration: null
   };

   function resetUI() {
       if (selectedVariantId) selectedVariantId.value = '';
       if (variantInfo) variantInfo.textContent = '';
       if (productPriceSpan) {
           productPriceSpan.textContent =
               '{{ $product->formatted_sale_price ?? $product->formatted_price }}';
       }
       if (addToCartBtn) addToCartBtn.disabled = defaultTotalStock <= 0;
       if (quantityInput) quantityInput.removeAttribute('max');
       if (availableStockInfo) availableStockInfo.textContent = defaultAvailableText;
   }

   function findMatchedVariant() {
       return variantMatrix.find(v =>
           (!selected.size || v.size === selected.size) &&
           (!selected.scent || v.scent === selected.scent) &&
           (!selected.concentration || v.concentration === selected.concentration)
       );
   }

   // Chọn biến thể theo từng nhóm (size / scent / concentration)
   document.querySelectorAll('.variant-group').forEach(group => {
       const type = group.dataset.type;

       group.querySelectorAll('.variant-option').forEach(btn => {
           btn.addEventListener('click', function () {
               // clear active trong group
               group.querySelectorAll('.variant-option').forEach(b => {
                   b.classList.remove('btn-primary', 'text-white');
                   b.classList.add('btn-outline-secondary');
               });

               // set active
               this.classList.remove('btn-outline-secondary');
               this.classList.add('btn-primary', 'text-white');

               selected[type] = this.dataset.value;

               const variant = findMatchedVariant();

               if (!variant) {
                   resetUI();
                   return;
               }

               // apply variant
               if (selectedVariantId) selectedVariantId.value = variant.id;

               if (variantInfo) {
                   let info = [];
                   if (variant.size) info.push('Kích thước: ' + variant.size);
                   if (variant.scent) info.push('Mùi: ' + variant.scent);
                   if (variant.concentration) info.push('Nồng độ: ' + variant.concentration);
                   info.push('Tồn kho: ' + variant.stock);
                   variantInfo.textContent = info.join(' | ');
               }

               if (productPriceSpan) {
                   productPriceSpan.textContent =
                       new Intl.NumberFormat('vi-VN').format(variant.price) + ' VNĐ';
               }

               if (addToCartBtn) {
                   addToCartBtn.disabled = variant.stock <= 0;
               }

               if (quantityInput) {
                   quantityInput.setAttribute('max', variant.stock);
                   if (parseInt(quantityInput.value) > variant.stock) {
                       quantityInput.value = variant.stock;
                   }
               }

               if (availableStockInfo) {
                   availableStockInfo.textContent =
                       variant.stock > 0
                           ? 'Có sẵn: ' + variant.stock + ' sản phẩm'
                           : 'Không còn hàng';
               }
           });
       });
   });

   // Nút tăng / giảm số lượng
   const decreaseBtn = document.querySelector('.quantity-decrease');
   const increaseBtn = document.querySelector('.quantity-increase');

   if (decreaseBtn && quantityInput) {
       decreaseBtn.addEventListener('click', function () {
           const currentValue = parseInt(quantityInput.value) || 1;
           if (currentValue > 1) {
               quantityInput.value = currentValue - 1;
           }
       });
   }

   if (increaseBtn && quantityInput) {
       increaseBtn.addEventListener('click', function () {
           const currentValue = parseInt(quantityInput.value) || 1;
           const max = parseInt(quantityInput.getAttribute('max')) || 100;
           if (currentValue < max) {
               quantityInput.value = currentValue + 1;
           }
       });
   }

});
</script>


@endsection



