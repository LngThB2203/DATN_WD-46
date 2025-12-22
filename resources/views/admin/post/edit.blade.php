@extends('admin.layouts.admin')

@section('content')
<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 text-primary">📝 <strong>Sửa bài viết</strong></h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('post.update', $post->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Ảnh đại diện hiện tại</label>
                    <div class="p-2 border rounded d-inline-block bg-light mb-2">
                        @if ($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" width="150" class="img-thumbnail shadow-sm">
                        @else
                            <span class="text-muted small italic">Chưa có ảnh</span>
                        @endif
                    </div>
                    <input type="file" name="image" class="form-control mt-2">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung</label>
                    <textarea name="content" id="content_summernote" class="form-control">{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="border-top pt-3 text-end">
                    <a href="{{ route('post.index') }}" class="btn btn-secondary px-4">Hủy</a>
                    <button class="btn btn-primary px-4">Cập nhật ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('styles')
{{-- Thêm CSS của Summernote --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border-radius: 0.25rem; 
    }
</style>
@endpush

@push('scripts')
{{-- Thêm jQuery (Summernote yêu cầu) và JS của Summernote --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // Khởi tạo Summernote trên ID đã đặt
        $('#content_summernote').summernote({
            placeholder: 'Nhập nội dung bài viết...',
            tabsize: 2,
            height: 300, 
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush