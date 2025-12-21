@extends('client.layouts.app')

@section('title', 'Đánh giá sản phẩm')

@section('content')
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
                                <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                    <option value="">Chọn</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
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
