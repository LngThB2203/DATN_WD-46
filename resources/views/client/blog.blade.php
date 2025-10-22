@extends('client.layouts.app')

@section('title', 'Blog')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-4">Bài viết mới</h2>
        <div class="row g-4">
            @for ($i = 1; $i <= 6; $i++)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100">
                        <a href="{{ route('blog.show', ['slug' => 'bai-viet-'.$i]) }}" class="text-decoration-none">
                            <img src="{{ asset('assets/client/img/product/product-'.(($i-1)%6+1).'.webp') }}" class="card-img-top" alt="Post {{$i}}">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><a href="{{ route('blog.show', ['slug' => 'bai-viet-'.$i]) }}">Bài viết {{ $i }}</a></h5>
                            <p class="card-text text-muted">Tóm tắt ngắn gọn nội dung bài viết.</p>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
@endsection
