@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Quản lý bài viết</h2>


    <a href="{{ route('post.create') }}" class="btn btn-success mb-3">+ Thêm bài viết</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Ảnh</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" width="80">
                    @endif
                </td>
                <td>
                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('post.delete', $post->id) }}" method="GET" class="d-inline">
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa bài viết này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $posts->links() }}
</div>
@endsection