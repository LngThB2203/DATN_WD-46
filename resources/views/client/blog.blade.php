@extends('client.layouts.app')

@section('title', 'Blog')

@section('content')
<section class="py-5">
    <div class="container-fluid container-xl">
    <div class="container mt-3">
        <div class="row"> 
            <div class="col-lg-8">
                <div class="row g-4"> 
                    @forelse($posts as $post)
                        <div class="col-12 col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                    <img 
                                        src="{{ $post->image ? asset('storage/' . $post->image) : asset('assets/client/img/default-post.webp') }}" 
                                        class="card-img-top rounded post-image-ratio"
                                        alt="{{ $post->title }}">
                                </a>
                                <div class="card-body"> 
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                                            {{-- Giữ giới hạn 1 dòng cho tiêu đề card --}}
                                            <span class="text-truncate d-block">{{ $post->title }}</span>
                                        </a>
                                    </h5>
                                    <div class="card-content-wrapper">
                                        <p class="card-text text-muted">{{ Str::limit($post->content, 100) }}</p> 
                                    </div>
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
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
            <div class="col-lg-4 sidebar-wrapper"> 
                <h4 class="fw-bold mb-3 text-primary">Bài viết mới nhất</h4>
                 <hr class="text-black-50 mt-2 mb-4"> 
                @foreach($latestPosts as $item)
        <div class="mb-3 pb-2 border-bottom border-light"> 
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
.container {
     overflow-x: hidden;
}

.sidebar-wrapper { 
  max-width: 100%;
  overflow: hidden; 
}

.card-content-wrapper {
  max-width: 100%; 
  overflow: hidden; 
  word-break: break-word; 
}

.card-content-wrapper img, 
.card-content-wrapper table {
  max-width: 100%; 
  height: auto;
}

.card-content-wrapper table {
  display: block;
  overflow-x: auto; 
  -webkit-overflow-scrolling: touch;
}

.post-image-ratio {
  width: 100%;
  aspect-ratio: 16 / 9; 
  object-fit: cover;
}
</style>
@endpush