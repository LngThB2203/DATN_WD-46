@extends('client.layouts.app')

@section('title', 'Điều khoản dịch vụ')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Điều khoản dịch vụ</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-3">Điều khoản dịch vụ</h2>
        <p class="text-muted">Nội dung điều khoản sẽ được cập nhật sau.</p>
    </div>
</section>
@endsection
