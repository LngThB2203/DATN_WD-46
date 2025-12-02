@extends('admin.layouts.admin')

@section('title', 'Danh sách Banner')

@section('content')
<div class="page-content">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">🖼️ <strong>Danh sách Banner </strong></h5>
    </div>
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
         @if(isset($alertMessage))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ $alertMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
         <a href="{{ route('banner.create') }}" class="btn btn-success btn-sm">+ Thêm Banner</a>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Hình ảnh</th>
                    <th>Liên kết</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th width="150">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($banners as $key => $banner)
                @php
                    $isExpired = $banner->end_date && $banner->end_date < now()->toDateString();
                @endphp
                <tr>
                    <td> {{ $key + 1 }} </td>
                    <td><img src="{{ asset('storage/'.$banner->image) }}" width="120" class="rounded"></td>
                    <td>{{ $banner->link }}</td>
                    <td>{{ $banner->start_date }}</td>
                    <td class="{{ $isExpired ? 'table-danger fw-bold' : '' }}">
                        {{ $banner->end_date }}
                    </td>
                    <td>
                        <a href="{{ route('banner.edit', $banner->id) }}" class="btn btn-sm btn-green">Sửa</a>
                        <form action="{{ route('banner.delete', $banner->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Chưa có banner nào</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection
