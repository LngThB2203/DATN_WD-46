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

        <!-- Bài viết liên quan -->
        <div class="related-posts mt-5 p-4 bg-light rounded shadow-sm">
            <h3 class="fw-bold mb-4">Bài viết liên quan</h3>
            @php
                $relatedPosts = \App\Models\Post::where('id', '!=', $post->id)
                                ->latest()
                                ->take(5)
                                ->get();
            @endphp
            <ul class="list-unstyled mb-0">
                @forelse($relatedPosts as $related)
                    <li class="mb-3 d-flex align-items-center">
                        <a href="{{ route('blog.show', $related->slug) }}" class="text-decoration-none text-dark d-flex align-items-center">
                            <img 
                                src="{{ $related->image ? asset('storage/' . $related->image) : asset('assets/client/img/default-post.webp') }}" 
                                alt="{{ $related->title }}"
                                class="rounded me-3"
                                style="width: 60px; height: 60px; object-fit: cover;"
                            >
                            <span class="fw-bold">{{ $related->title }}</span>
                        </a>
                    </li>
                @empty
                    <li>Chưa có bài viết liên quan.</li>
                @endforelse
            </ul>
        </div>

    </div>
</section>
@endsection
