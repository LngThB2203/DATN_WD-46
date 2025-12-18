@extends('client.layouts.app')

@section('title', $post->title)

@section('content')
<section class="py-2">
    <section class="py-2 border-bottom bg-light">
    <div class="container-fluid container-xl">
       
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">

        <div class="row g-4">
            <div class="col-lg-8 col-12"> 
                @if($post->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded shadow-sm w-100" alt="{{ $post->title }}">
                    </div>
                @endif
                <div class="text-center mb-4">
                    <h1 class="fw-bold display-5 mb-2">{{ $post->title }}</h1> 
                    <p class="text-muted mb-4 small">
                        <i class="fas fa-calendar-alt me-1"></i> Ngày đăng: {{ $post->created_at->format('d/m/Y') }}
                    </p>
                </div>
                <hr class="text-black-50 mt-2 mb-4">
                
                <div class="post-content mb-5 text-start">
                    {!! $post->content !!}
                </div>
            </div> <div class="col-lg-4 sidebar-wrapper"> 
                <h4 class="fw-bold mb-3 text-primary">Bài viết</h4> 
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
            </div> </div> </div>
</section>
@endsection