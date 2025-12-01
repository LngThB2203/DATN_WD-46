@extends('admin.layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="page-content">

    <!-- Start Container Fluid -->
    <div class="container-xxl">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Product Images</h5>
                        @if($product->galleries->count() > 0)
                            <div class="row">
                                @foreach($product->galleries as $gallery)
                                    <div class="col-6 mb-3">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $gallery->image_path) }}" 
                                                 alt="{{ $gallery->alt_text }}" 
                                                 class="img-fluid rounded border"
                                                 style="width: 100%; height: 150px; object-fit: cover;">
                                            @if($gallery->is_primary)
                                                <span class="badge bg-primary position-absolute top-0 start-0">Primary</span>
                                            @endif
                            </div>
                        </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="bx bx-image fs-48 mb-3"></i>
                                <p>No images available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Product Information</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit"></i> Edit Product
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-arrow-back"></i> Back to List
                            </a>
                        </div>
                                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Product Name:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->name }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>SKU:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->sku ?? 'N/A' }}
                            </div>
                                    </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Category:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->category->category_name ?? 'N/A' }}
                            </div>
                                    </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Price:</strong>
                            </div>
                            <div class="col-sm-9">
                                <span class="fw-bold text-primary fs-5">
                                    {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                </span>
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <br>
                                    <span class="text-decoration-line-through text-muted">
                                        {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                    </span>
                                    <br>
                                    <span class="text-danger fw-bold">
                                        Sale: {{ number_format($product->sale_price, 0, ',', '.') }} VNĐ
                                    </span>
                                    <br>
                                    <span class="badge bg-success">
                                        {{ $product->discount_percentage }}% OFF
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Status:</strong>
                    </div>
                            <div class="col-sm-9">
                                @if($product->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                    </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Created:</strong>
                        </div>
                            <div class="col-sm-9">
                                {{ $product->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Updated:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->updated_at->format('d/m/Y H:i') }}
                        </div>
                        </div>

                        @if($product->description)
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>Description:</strong>
                            </div>
                            <div class="col-sm-9">
                                <div class="border rounded p-3 bg-light">
                                    {{ $product->description }}
                        </div>
                        </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection