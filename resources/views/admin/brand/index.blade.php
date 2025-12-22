@extends('admin.layouts.admin')

@section('content')
<div class="page-content"> {{-- Thêm class này để đồng bộ padding với trang Banner --}}
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">🏢 <strong>Danh sách Thương hiệu</strong></h5>
        <a href="{{ route('brand.create') }}" class="btn btn-success btn-sm">+ Thêm Thương hiệu</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-body table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 180px;">Tên thương hiệu</th>
                    <th style="width: 120px;">Logo</th>
                    <th style="width: 120px;">Xuất xứ</th>
                    <th>Mô tả</th>
                    <th style="width: 160px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $key => $brand)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="fw-bold text-primary">{{ $brand->name }}</td>
                    <td>
                        @if ($brand->image)
                            <img src="{{ asset('storage/' . $brand->image) }}" 
                                 style="width: 80px; height: 50px; object-fit: contain;" 
                                 class="img-thumbnail shadow-sm">
                        @else
                            <span class="badge bg-light text-dark">No Logo</span>
                        @endif
                    </td>
                    <td><span class="badge bg-info text-white">{{ $brand->origin }}</span></td>
                    <td>
                        <div style="max-width: 400px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0 auto; text-align: left;">
                            {{ $brand->description }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('brand.edit', $brand->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                            
                            <form action="{{ route('brand.delete', $brand->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này?')">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection