@extends('admin.layouts.admin')

@section('title', 'Chi tiết đánh giá')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2">
                        <h4 class="card-title flex-grow-1">Chi tiết đánh giá #{{ $review->id }}</h4>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-secondary">Quay lại</a>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="mb-2"><strong>Sản phẩm:</strong> {{ $review->product->name ?? '-' }}</div>
                                    <div class="mb-2"><strong>Người dùng:</strong> {{ $review->user->name ?? 'Khách' }}</div>
                                    <div class="mb-2"><strong>Số sao:</strong> {{ $review->rating }}/5</div>
                                    <div class="mb-2"><strong>Trạng thái:</strong>
                                        <span class="badge {{ $review->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $review->status ? 'Hiện' : 'Ẩn' }}
                                        </span>
                                    </div>
                                    <div class="mb-0"><strong>Thời gian:</strong>
                                        @if($review->created_at)
                                            {{ $review->created_at->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="mb-2"><strong>Đơn hàng liên quan:</strong>
                                        @if($review->order_id)
                                            #{{ $review->order_id }}
                                            <div class="mt-1">
                                                <a href="{{ route('admin.orders.show', $review->order_id) }}" class="btn btn-sm btn-outline-primary">Xem lịch sử đơn hàng</a>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="mb-0"><strong>Hành động:</strong>
                                        <div class="d-flex gap-2 mt-2">
                                            <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-warning" type="submit">
                                                    {{ $review->status ? 'Ẩn đánh giá' : 'Hiện lại đánh giá' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <div class="mb-2"><strong>Nội dung:</strong></div>
                                    <div style="white-space: pre-wrap">{{ $review->comment ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
