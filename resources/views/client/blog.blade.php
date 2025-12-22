@extends('client.layouts.app')

@section('title', 'Blog')

@section('content')
<section class="py-3 border-bottom bg-light">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Bài viết</a></li>
            </ol>
        </nav>
    </div>
</section>
    <div class="container-fluid container-xl">

        <div class="row g-4">
            
            <div class="col-lg-8">
                
                <div class="row g-4"> 
                    @forelse($posts as $post)
                        <div class="col-12 col-md-6 d-flex align-items-stretch">
                            <div class="card w-100 h-100 shadow-sm border-0">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                    <img 
                                        src="{{ $post->image ? asset('storage/' . $post->image) : asset('assets/client/img/default-post.webp') }}" 
                                        class="card-img-top rounded post-image-ratio"
                                        alt="{{ $post->title }}">
                                </a>
                                <div class="card-body d-flex flex-column"> 
                                    <h5 class="card-title fw-bold mb-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" 
                                        class="text-dark text-decoration-none text-truncate-2-lines d-block">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <small class="text-muted mb-3">
                                        <i class="fas fa-calendar-alt me-1"></i> Ngày đăng: {{ $post->created_at->format('d/m/Y') }}
                                    </small>
                                    <p class="card-text text-muted flex-grow-1 text-truncate-3-lines">
                                        {{ Str::limit(strip_tags($post->content), 100) }}
                                    </p> 
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-primary w-100">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="alert alert-info text-center">Chưa có bài viết nào.</p>
                        </div>
                    @endforelse
                </div>
                
                {{-- Phân trang --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $posts->links() }}
                </div>
            </div> 
            <div class="col-lg-4 sidebar-wrapper"> 
                <h4 class="fw-bold mb-3 text-primary">Bài viết </h4>
                <hr class="text-black-50 mt-2 mb-4"> 
                @foreach($latestPosts as $item)
                    <div class="mb-3 pb-2 border-bottom border-black-50"> 
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
.container-fluid.container-xl {
    overflow-x: hidden;
}
.sidebar-wrapper { 
    max-width: 100%;
    overflow: hidden; 
}
.d-flex.align-items-stretch .card {
    height: 100%;
}
.post-image-ratio {
    width: 100%;
    aspect-ratio: 16 / 9; 
    object-fit: cover;
}
.text-truncate-2-lines {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2; 
    -webkit-box-orient: vertical; 
}
.text-truncate-3-lines {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3; 
    -webkit-box-orient: vertical;  
}
</style>
@endpush
@push('scripts')
<script>
</script>
@endpush
