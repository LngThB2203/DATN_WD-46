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
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label>Nội dung</label>
            <textarea name="content" class="form-control" rows="5">{{ old('content') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('post.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
