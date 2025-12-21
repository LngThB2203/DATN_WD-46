@extends('admin.layouts.admin')

@section('title', 'Danh sách biến thể')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- Hiển thị thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách biến thể</h4>
                <a href="{{ route('variants.create') }}" class="btn btn-sm btn-primary">
                    + Thêm biến thể
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Size</th>
                                <th>Mùi</th>
                                <th>Nồng độ</th>
                                <th>SKU</th>
                                <th>Tồn kho</th>
                                <th>Giá+</th>
                                <th>Giới tính</th>
                                <th width="140">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($variants as $v)
                            <tr>
                                <td>{{ $v->id }}</td>

                                <td>
                                    @if($v->image)
                                        <img src="{{ asset('storage/'.$v->image) }}" width="55" height="55" class="rounded" style="object-fit: cover;">
                                    @else
                                        <span class="text-muted">No-img</span>
                                    @endif
                                </td>

                                <td>{{ $v->product->name }}</td>
                                <td>{{ $v->size->size_name ?? $v->size->name ?? '-' }}</td>
                                <td>{{ $v->scent->scent_name ?? $v->scent->name ?? '-' }}</td>
                                <td>{{ $v->concentration->concentration_name ?? $v->concentration->name ?? '-' }}</td>
                                <td>{{ $v->sku }}</td>
                                <td>{{ $v->stock }}</td>
                                <td>{{ number_format($v->price_adjustment ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if($v->gender === 'male')
                                        <span class="badge bg-primary">Nam</span>
                                    @elseif($v->gender === 'female')
                                        <span class="badge bg-danger">Nữ</span>
                                    @else
                                        <span class="badge bg-secondary">Unisex</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('variants.edit', $v->id) }}" class="btn btn-sm btn-warning">Sửa</a>

                                        <form method="POST" action="{{ route('variants.destroy', $v->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa biến thể này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Chưa có biến thể nào</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $variants->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
