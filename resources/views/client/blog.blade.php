@extends('client.layouts.app')

@section('title', 'Blog')

@section('content')
<section class="py-5 bg-light">
    <div class="container">

        <!-- Hiển thị số kết quả tìm kiếm -->
        @if(request('search'))
            <p class="text-muted mb-3">
                Kết quả tìm kiếm cho: "<strong>{{ request('search') }}</strong>" ({{ $posts->total() }} bài viết)
            </p>
        @endif

        <h2 class="fw-bold mb-4 text-center">Bài viết </h2>

        <!-- Danh sách bài viết -->
        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                            <img 
                                src="{{ $post->image ? asset('storage/' . $post->image) : asset('assets/client/img/default-post.webp') }}" 
                                class="card-img-top rounded" 
                                alt="{{ $post->title }}"
                                style="height: 150px; object-fit: cover; transition: transform 0.3s;"
                                onmouseover="this.style.transform='scale(1.05)';" 
                                onmouseout="this.style.transform='scale(1)';"
                            >
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                                    {{ $post->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted">{{ Str::limit($post->content, 120) }}</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center">Chưa có bài viết nào.</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $posts->links() }}
        </div>

    </div>
</section>
@endsection