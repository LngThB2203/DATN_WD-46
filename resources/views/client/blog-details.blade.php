@extends('client.layouts.app')

@section('title', $slug ?? 'Chi tiết bài viết')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $slug }}</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h1 class="fw-bold mb-3 text-capitalize">{{ str_replace('-', ' ', $slug ?? 'bai-viet') }}</h1>
        <p class="text-muted">Nội dung bài viết sẽ được hiển thị ở đây.</p>
    </div>
</section>
@endsection
