@extends('client.layouts.app')

@section('title', $category->category_name ?? 'Danh mục sản phẩm')

@section('content')

{{-- BREADCRUMB --}}
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>

                @if ($category)
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->category_name }}</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">Danh mục</li>
                @endif
            </ol>
        </nav>
    </div>
</section>


<section class="py-5">
    <div class="container-fluid container-xl">

        {{-- ============================
        HIỂN THỊ DANH SÁCH DANH MỤC
        =============================== --}}
        @if (!$category)
        <h3 class="mb-4">Danh mục sản phẩm</h3>

        <div class="row g-4">
            @foreach ($categories as $cate)
            <div class="col-6 col-sm-4 col-lg-3">
                <a href="{{ route('category.show', $cate->slug) }}" class="btn btn-light w-100 py-3">
                    {{ $cate->category_name }}
                </a>
            </div>
            @endforeach
        </div>
        @endif

        @if ($category)
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <h3 class="mb-0">{{ $category->category_name }}</h3>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tìm</button>
                    @if(request('search'))
                    <a href="{{ route('category.show', $category->slug) }}" class="btn btn-outline-secondary">Xóa</a>
                    @endif
                </form>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex justify-content-end">
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="sort" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="">Sắp xếp</option>
                        <option value="new" @if(request('sort')=='new' ) selected @endif>Mới nhất</option>
                        <option value="asc" @if(request('sort')=='asc' ) selected @endif>Giá tăng dần</option>
                        <option value="desc" @if(request('sort')=='desc' ) selected @endif>Giá giảm dần</option>
                    </select>
                </form>
            </div>
            @forelse ($products as $product)
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100">

                    <a href="{{ route('product.show', $product->slug) }}">
                        <img src="{{ $product->primaryImageModel
                                    ? asset('storage/' . $product->primaryImageModel->image_path)
                                    : asset('assets/client/img/no-image.jpg') }}" class="card-img-top"
                            alt="{{ $product->name }}" style="width: 100%; height: 250px; object-fit: cover;">
                    </a>

                    <div class="card-body">
                        <h6 class="mb-1">{{ $product->name }}</h6>

                        <p class="text-danger fw-bold mb-2">
                            {{ number_format($product->price) }}₫
                        </p>

                        {{-- Hiển thị tồn kho --}}
                        <p class="text-muted small mb-2">
                            Tồn kho: <strong>{{ $product->stock_quantity }}</strong>
                        </p>

                        <a href="{{ route('product.show', $product->slug) }}"
                            class="btn btn-sm btn-outline-primary w-100">
                            Xem chi tiết
                        </a>
                    </div>

                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">Không có sản phẩm nào trong danh mục này.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
        @endif


    </div>
</section>

@endsection
