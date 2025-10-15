@extends('client.layouts.app')

@section('title', 'Thông tin vận chuyển')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thông tin vận chuyển</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-3">Thông tin vận chuyển</h2>
        <p class="text-muted">Chính sách và thời gian giao hàng sẽ được cập nhật sau.</p>
    </div>
</section>
@endsection
