@extends('admin.layouts.admin')

@section('title', 'Thêm mã giảm giá')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Thêm mã giảm giá</h4>
                        <a href="{{ route('admin.discounts.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.discounts.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                               value="{{ old('code') }}" placeholder="VD: SALE2024" required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Mã sẽ được chuyển thành chữ hoa</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                        <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                                            <option value="">Chọn loại</option>
                                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
                                        </select>
                                        @error('discount_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Giá trị giảm giá <span class="text-danger">*</span></label>
                                        <input type="number" name="discount_value" step="0.01" min="0" 
                                               class="form-control @error('discount_value') is-invalid @enderror" 
                                               value="{{ old('discount_value') }}" placeholder="10 hoặc 10000" required>
                                        @error('discount_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Nhập % (nếu là phần trăm) hoặc số tiền (nếu là cố định)</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                                        <input type="number" name="min_order_value" step="0.01" min="0" 
                                               class="form-control @error('min_order_value') is-invalid @enderror" 
                                               value="{{ old('min_order_value') }}" placeholder="Không bắt buộc">
                                        @error('min_order_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ngày bắt đầu</label>
                                        <input type="date" name="start_date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ngày hết hạn</label>
                                        <input type="date" name="expiry_date" 
                                               class="form-control @error('expiry_date') is-invalid @enderror" 
                                               value="{{ old('expiry_date') }}">
                                        @error('expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Giới hạn lượt sử dụng</label>
                                        <input type="number" name="usage_limit" min="1" 
                                               class="form-control @error('usage_limit') is-invalid @enderror" 
                                               value="{{ old('usage_limit') }}" placeholder="Để trống = không giới hạn">
                                        @error('usage_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="active" id="active" 
                                                   {{ old('active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active">
                                                Kích hoạt mã giảm giá
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Lưu
                                </button>
                                <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
                                    Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

