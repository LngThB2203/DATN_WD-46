@extends('client.layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
<section class="py-5 container">
    <h3 class="mb-4">
        Kết quả tìm kiếm cho: <strong>"{{ $keyword }}"</strong>
        <small class="text-muted">({{ $type == 'absolute' ? 'Tuyệt đối' : 'Tương đối' }})</small>
    </h3>

    @if($products->count() > 0)
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-3">
                    <div class="card p-2">
                        <img src="{{ asset('storage/' . $product->primaryImage()) }}" class="card-img-top" alt="">
                        <div class="card-body">
                            <h6>{{ $product->name }}</h6>
                            <p>{{ number_format($product->price) }} đ</p>
                            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-primary w-100">

                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
    @endif
</section>
@endsection
