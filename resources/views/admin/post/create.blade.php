
@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Thêm bài viết</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label>Ảnh đại diện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="content_summernote">Nội dung</label>
            <textarea name="content" id="content_summernote" class="form-control">{{ old('content') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('post.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
@push('styles')
{{-- 1. Thêm CSS của Summernote --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Tuỳ chỉnh nếu cần */
    .note-editor.note-frame {
        border-radius: 0.25rem; /* Giúp editor nhìn đồng bộ với form-control */
    }
</style>
@endpush

@push('scripts')
{{-- 2. Thêm jQuery (Summernote yêu cầu) và JS của Summernote --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // 3. Khởi tạo Summernote trên ID đã đặt
        $('#content_summernote').summernote({
            placeholder: 'Nhập nội dung bài viết...',
            tabsize: 2,
            height: 300, // Chiều cao mặc định của editor
            // Tuỳ chọn ngôn ngữ Tiếng Việt (nếu bạn đã thêm file ngôn ngữ)
            // lang: 'vi-VN', 
            toolbar: [
                // Cấu hình toolbar đơn giản hơn (sử dụng summernote-lite)
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
