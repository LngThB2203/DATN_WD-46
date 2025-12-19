@extends('client.layouts.app')

@section('title', 'Sản phẩm yêu thích')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <h2 class="mb-0">Sản phẩm yêu thích</h2>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        @if($items->isEmpty())
            <p class="text-muted mb-0">Bạn chưa có sản phẩm yêu thích nào.</p>
        @else
            <div class="row g-3">
                @foreach($items as $item)
                    @php $product = $item->product; @endphp
                    @if($product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="text-decoration-none">
                                <div class="card h-100">
                                    @php
                                        $img = $product->primaryImage()
                                            ? asset('storage/'.$product->primaryImage()->image_path)
                                            : ($product->image
                                                ? asset('storage/'.$product->image)
                                                : asset('assets/client/img/product/product-1.webp'));
                                    @endphp
                                    <img src="{{ $img }}" class="card-img-top" alt="{{ $product->name }}">
                                    <div class="card-body">
                                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                        <div class="small text-primary">{{ $product->formatted_sale_price ?? $product->formatted_price }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
