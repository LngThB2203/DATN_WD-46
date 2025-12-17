@extends('admin.layouts.admin')

@section('title', 'Sửa biến thể sản phẩm')

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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Sửa biến thể #{{ $variant->id }} - {{ $product->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="row">
                        {{-- Kích thước, Mùi hương, Nồng độ --}}
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Kích thước</label>
                            <select name="size_id" class="form-select">
                                <option value="">Không</option>
                                @foreach($sizes as $s)
                                <option value="{{ $s->id }}" @selected($variant->size_id == $s->id)>
                                    {{ $s->size_name ?? $s->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Mùi hương</label>
                            <select name="scent_id" class="form-select">
                                <option value="">Không</option>
                                @foreach($scents as $s)
                                <option value="{{ $s->id }}" @selected($variant->scent_id == $s->id)>
                                    {{ $s->scent_name ?? $s->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Nồng độ</label>
                            <select name="concentration_id" class="form-select">
                                <option value="">Không</option>
                                @foreach($concentrations as $c)
                                <option value="{{ $c->id }}" @selected($variant->concentration_id == $c->id)>
                                    {{ $c->concentration_name ?? $c->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SKU --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $variant->sku) }}">
                        </div>

                        {{-- Giá điều chỉnh --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giá điều chỉnh (+/-)</label>
                            <input type="number" step="0.01" name="price_adjustment" class="form-control"
                                value="{{ old('price_adjustment', $variant->price_adjustment) }}">
                        </div>

                        {{-- Tồn kho (readonly) --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Tồn kho</label>
                            <input type="text" class="form-control" value="{{ $variant->stock }}" disabled>
                            <small class="text-muted">Tồn kho được quản lý trong Inventory</small>
                        </div>

                        {{-- Giới tính --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="unisex" @selected($variant->gender == 'unisex')>Unisex</option>
                                <option value="male" @selected($variant->gender == 'male')>Nam</option>
                                <option value="female" @selected($variant->gender == 'female')>Nữ</option>
                            </select>
                        </div>

                        {{-- Ảnh hiện tại --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Ảnh hiện tại</label><br>
                            @if($variant->image)
                                <img src="{{ asset('storage/'.$variant->image) }}" width="120" height="120" 
                                     class="rounded mb-2" style="object-fit: cover;">
                            @else
                                <span class="text-muted">Không có ảnh</span>
                            @endif
                        </div>

                        {{-- Ảnh mới --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Ảnh mới</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Chấp nhận: JPG, PNG, WEBP (tối đa 2MB). Để trống nếu không muốn thay đổi.</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Cập nhật
                        </button>
                        <a href="{{ route('variants.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
