@extends('admin.layouts.admin')

@section('title', 'List Product')

@section('content')
<div class="page-content">

    <!-- Start Container Fluid -->
    <div class="container-fluid">

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

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">All Product List</h4>

                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                            Add Product
                        </a>

                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                This Month
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="#" class="dropdown-item">Download</a>
                                <!-- item-->
                                <a href="#" class="dropdown-item">Export</a>
                                <!-- item-->
                                <a href="#" class="dropdown-item">Import</a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover table-centered">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th style="width: 20px;">
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="customCheck1">
                                                <label class="form-check-label" for="customCheck1"></label>
                                            </div>
                                        </th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $product->id }}">
                                                <label class="form-check-label" for="customCheck{{ $product->id }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                    @if($product->primaryImage())
                                                        <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}" alt="{{ $product->name }}" class="avatar-md">
                                                    @else
                                                        <i class="bx bx-image fs-24 text-muted"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('products.show', $product) }}" class="text-dark fw-medium fs-15">{{ $product->name }}</a>
                                                    <p class="text-muted mb-0 mt-1 fs-13">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @if($product->sale_price && $product->sale_price < $product->price)
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span><br>
                                                    <span class="text-danger fw-bold">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</span>
                                                @else
                                                    <span class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($product->status)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-light btn-sm" title="View">
                                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-soft-danger btn-sm" title="Delete">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-package fs-48 mb-3"></i>
                                                <p>Không có sản phẩm nào</p>
                                                <a href="{{ route('products.create') }}" class="btn btn-primary">Thêm sản phẩm đầu tiên</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                    @if($products->hasPages())
                    <div class="card-footer border-top">
                        <nav aria-label="Page navigation example">
                            {{ $products->links() }}
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

