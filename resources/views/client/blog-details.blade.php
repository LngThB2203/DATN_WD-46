@extends('client.layouts.app')

@section('title', $post->title)

@section('content')
<!-- Breadcrumb -->
<section class="py-4 border-bottom bg-light">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Main content -->
<section class="py-5">
    <div class="container-fluid container-xl">

        <div class="row g-4">
            <!-- Ảnh bên trái -->
            <div class="col-lg-5 col-12">
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" 
                         class="img-fluid rounded shadow-sm w-100 mb-4 mb-lg-0" 
                         alt="{{ $post->title }}">
                @else
                    <img src="{{ asset('assets/client/img/default-post.webp') }}" 
                         class="img-fluid rounded shadow-sm w-100 mb-4 mb-lg-0" 
                         alt="{{ $post->title }}">
                @endif
            </div>

            <!-- Nội dung bên phải -->
            <div class="col-lg-7 col-12">
                <h1 class="fw-bold display-4 mb-3">{{ $post->title }}</h1>
                <div class="post-content mb-5">
                    {!! $post->content !!}
                </div>
            </div>
        </div>

        

    </div>
</section>
@endsection
