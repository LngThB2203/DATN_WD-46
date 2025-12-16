@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Sửa bài viết</h2>

    <form method="POST" action="{{ route('post.update', $post->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
        </div>

        <div class="mb-3">
            <label>Ảnh đại diện</label><br>
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" width="100" class="mb-2"><br>
            @endif
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label for="content_summernote">Nội dung</label>
            <textarea name="content" id="content_summernote" class="form-control">{{ old('content', $post->content) }}</textarea>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('post.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
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
