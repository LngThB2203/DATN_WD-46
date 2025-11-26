@extends('client.layouts.app')

@section('title', $post->title)

@section('content')

<section>
    <div class="container-fluid container-xl">
        <div class="row g-4"> 
            <div class="col-lg-8 col-12">
                <h1 class="fw-bold mb-4">{{ $post->title }}</h1>
                <p class="text-muted mb-2 small">
                    <i class="fas fa-calendar-alt me-1"></i> Ngày đăng: {{ $post->created_at->format('d/m/Y') }}
                </p>
                 <hr class="text-black-50 mt-2 mb-4">
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid mb-4 rounded" alt="{{ $post->title }}">
                @endif
                <div class="post-content content-style">
                    {!! $post->content !!}
                </div>
            </div>
            <div class="col-lg-4 sidebar-wrapper"> 
                <h4 class="fw-bold mb-3 text-primary">Bài viết mới nhất</h4> 
                 <hr class="text-black-50 mt-2 mb-4">
                @foreach($latestPosts as $item)
                    <div class="mb-3 pb-2 border-bottom border-black-50 "> 
                     <a href="{{ route('blog.show', $item->slug) }}" class="text-decoration-none">
                    <h6 class="mb-1" style="color: #1b8db0;"> 
                    <span class="text-truncate d-block">{{ $item->title }}</span> 
                </h6>
            </a>
        </div>
    @endforeach
            </div>

        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
.sidebar-wrapper { 
    max-width: 100%;
    overflow: hidden; 
}
.text-truncate-2-lines {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2; 
    -webkit-box-orient: vertical;  
}
.post-content img {
    max-width: 100%; 
    height: auto;
}
.post-content table {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    display: block;
}
</style>
@endpush