@extends('admin.layouts.admin')

@section('title', 'Thêm biến thể sản phẩm')

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
                <h4 class="card-title mb-0">Thêm biến thể sản phẩm</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('variants.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Sản phẩm --}}
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="mb-3">
                            <label class="form-label">Sản phẩm</label>
                            <input type="text" class="form-control" value="{{ $product->name }}" disabled>
                        </div>


                        {{-- Kích thước, Mùi hương, Nồng độ --}}
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Kích thước</label>
                            <select name="size_id" class="form-select">
                                <option value="">Không</option>
                                @foreach($sizes as $s)
                                <option value="{{ $s->id }}" {{ old('size_id')==$s->id ? 'selected' : '' }}>
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
                                <option value="{{ $s->id }}" {{ old('scent_id')==$s->id ? 'selected' : '' }}>
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
                                <option value="{{ $c->id }}" {{ old('concentration_id')==$c->id ? 'selected' : '' }}>
                                    {{ $c->concentration_name ?? $c->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SKU --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}"
                                placeholder="VD: PF-D-50-FRU-1" required>
                            <small class="text-muted">Mã SKU duy nhất cho biến thể này</small>
                        </div>

                        {{-- Giá điều chỉnh --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giá điều chỉnh (+/-)</label>
                            <input type="number" step="0.01" name="price_adjustment" class="form-control"
                                value="{{ old('price_adjustment', 0) }}" placeholder="0">
                            <small class="text-muted">Số tiền cộng hoặc trừ vào giá sản phẩm gốc</small>
                        </div>

                        {{-- Giới tính --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select" required>
                                <option value="unisex" {{ old('gender', 'unisex' )=='unisex' ? 'selected' : '' }}>Unisex
                                </option>
                                <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>

                        {{-- Ảnh biến thể --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Ảnh biến thể</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Chấp nhận: JPG, PNG, WEBP (tối đa 2MB)</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Lưu
                        </button>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
