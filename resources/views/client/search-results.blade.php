@extends('client.layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
<div class="container py-5">
    <h3 class="mb-4">Kết quả tìm kiếm cho: <strong>{{ $keyword }}</strong></h3>

    @if ($products->count() == 0)
        <p>Không tìm thấy sản phẩm phù hợp.</p>
    @else
        <div class="row g-4">
            @foreach ($products as $product)
                <div class="col-md-3">
                    <div class="card h-100">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset('storage/' . optional($product->primaryImage)->image_path) }}"
                                 class="card-img-top"
                                 onerror="this.src='/assets/client/img/no-image.png'">
                        </a>
                        <div class="card-body text-center">
                            <h5>{{ $product->name }}</h5>
                            <p class="text-danger fw-bold">{{ number_format($product->price) }} VNĐ</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
