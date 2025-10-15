@extends('client.layouts.app')

@section('title', 'Về chúng tôi')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Về chúng tôi</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-3">Về eStore</h2>
        <p class="text-muted">Giới thiệu ngắn gọn về cửa hàng nước hoa trực tuyến của bạn.</p>
    </div>
</section>
@endsection
