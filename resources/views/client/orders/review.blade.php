@extends('client.layouts.app')

@section('title', 'Đánh giá sản phẩm')

@section('content')
<style>
    .order-review-star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 0.25rem;
        line-height: 1;
    }

    .order-review-star-rating input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        margin: 0;
        padding: 0;
        pointer-events: none;
    }

    .order-review-star-rating label {
        cursor: pointer;
        font-size: 2rem;
        color: #d0d5dd;
        transition: transform 0.12s ease, color 0.12s ease;
        user-select: none;
    }

    .order-review-star-rating label:hover {
        transform: translateY(-1px);
    }

    .order-review-star-rating input[type="radio"]:checked ~ label,
    .order-review-star-rating label:hover,
    .order-review-star-rating label:hover ~ label {
        color: #f4c430;
    }

    .order-review-star-rating.is-invalid label {
        color: #dc3545;
    }

    .order-review-star-rating:focus-within {
        outline: 2px solid rgba(13, 110, 253, 0.25);
        outline-offset: 4px;
        border-radius: 0.5rem;
        padding: 0.25rem;
    }
</style>
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.show', $order->id) }}">Chi tiết đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đánh giá sản phẩm</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Đánh giá sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($product->primaryImage())
                                <img src="{{ asset('storage/'.$product->primaryImage()->image_path) }}" alt="{{ $product->name }}" class="rounded" style="width:60px;height:60px;object-fit:cover;">
                            @endif
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <div class="small text-muted">Mã đơn: #{{ str_pad($order->id,6,'0',STR_PAD_LEFT) }}</div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('orders.review.store', [$order->id, $product->id]) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="rating" class="form-label">Chấm điểm (1-5)</label>
                                <div class="order-review-star-rating @error('rating') is-invalid @enderror" role="radiogroup" aria-label="Chọn số sao">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input
                                            type="radio"
                                            id="rating-{{ $i }}"
                                            name="rating"
                                            value="{{ $i }}"
                                            {{ (string) old('rating') === (string) $i ? 'checked' : '' }}
                                            {{ $i === 5 ? 'required' : '' }}
                                        >
                                        <label for="rating-{{ $i }}" title="{{ $i }} sao" aria-label="{{ $i }} sao">★</label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">Nhận xét (tuỳ chọn)</label>
                                <textarea id="comment" name="comment" rows="3" class="form-control @error('comment') is-invalid @enderror" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
