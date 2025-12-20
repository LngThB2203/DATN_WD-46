@extends('admin.layouts.admin')

@section('title', 'Sản phẩm đã xóa')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Sản phẩm đã xóa</h4>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            ← Quay lại danh sách
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá</th>
                            <th>Ngày xóa</th>
                            <th width="180">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>

                                <td>
                                   {{ $product->category->category_name ?? '-' }}
                                </td>

                                <td>{{ $product->brand?->name ?? 'Không có thương hiệu' }}</td>


                                <td>
                                    {{ number_format($product->price) }} đ
                                </td>

                                <td>
                                    {{ $product->deleted_at?->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{-- Khôi phục --}}
                                    <form action="{{ route('products.restore', $product->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm">
                                            Khôi phục
                                        </button>
                                    </form>

                                    {{-- Xóa vĩnh viễn --}}
                                    <form action="{{ route('products.forceDelete', $product->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Xóa vĩnh viễn sản phẩm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            Xóa vĩnh viễn
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Không có sản phẩm nào bị xóa
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}

        </div>
    </div>
</div>
@endsection
