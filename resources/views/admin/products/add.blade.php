@extends('admin.layouts.admin')

@section('title', 'Add Product')

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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
                    <div class="row">
                         <div class="col-xl-3 col-lg-4">
                              <div class="card">
                                   <div class="card-body">
                            <div id="image-preview" class="text-center">
                                <i class="bx bx-image fs-48 text-muted mb-3"></i>
                                <p class="text-muted">Chưa có ảnh nào được chọn</p>
                                             </div>
                                             <div class="mt-3">
                                <h5 class="text-dark fw-medium">Upload Images</h5>
                                <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Chọn nhiều ảnh (JPG, PNG, GIF)</small>
                                        </div>
                                   </div>
                              </div>
                         </div>

                <div class="col-xl-9 col-lg-8">
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Product Information</h4>
                                   </div>
                                   <div class="card-body">
                                        <div class="row">
                                             <div class="col-lg-6">
                                                       <div class="mb-3">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                               placeholder="Product Name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                                       </div>
                                             </div>
                                             <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror" 
                                               placeholder="Product SKU" value="{{ old('sku') }}">
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                             </div>
                                        </div>
                                        <div class="row">
                                <div class="col-lg-6">
                                                       <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                            <option value="">Choose a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                                       </div>
                                             </div>
                                <div class="col-lg-6">
                                                       <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" {{ old('status') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">Active Status</label>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <div class="mb-3">
                                                       <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                                                  name="description" rows="5" placeholder="Product description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                             </div>
                                        </div>
                                   </div>
                              </div>
                    </div>
                    
                              <div class="card">
                                   <div class="card-header">
                                        <h4 class="card-title">Pricing Details</h4>
                                   </div>
                                   <div class="card-body">
                                        <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">VNĐ</span>
                                            <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" 
                                                   placeholder="0" value="{{ old('price') }}" required step="0.01" min="0">
                                                       </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                             </div>
                                                       </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label">Sale Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">VNĐ</span>
                                            <input type="number" id="sale_price" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" 
                                                   placeholder="0" value="{{ old('sale_price') }}" step="0.01" min="0">
                                             </div>
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                                       </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                    
                              <div class="p-3 bg-light mb-3 rounded">
                                   <div class="row justify-content-end g-2">
                                        <div class="col-lg-2">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                                        </div>
                                        <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">Create Product</button>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
        </form>
               </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        console.log('Files selected:', files.length);
        imagePreview.innerHTML = '';

        if (files.length > 0) {
            Array.from(files).forEach((file, index) => {
                console.log('Processing file:', file.name, 'Size:', file.size, 'Type:', file.type);
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-fluid rounded mb-2';
                        img.style.maxHeight = '100px';
                        img.style.objectFit = 'cover';
                        img.title = file.name;
                        
                        if (index === 0) {
                            imagePreview.innerHTML = '';
                            const label = document.createElement('p');
                            label.className = 'text-muted mb-2';
                            label.textContent = 'Primary Image:';
                            imagePreview.appendChild(label);
                        }
                        
                        imagePreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    console.log('Invalid file type:', file.type);
                }
            });
        } else {
            imagePreview.innerHTML = '<i class="bx bx-image fs-48 text-muted mb-3"></i><p class="text-muted">Chưa có ảnh nào được chọn</p>';
        }
    });

    // Add form submission debug
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const files = document.getElementById('images').files;
            console.log('Form submitting with files:', files.length);
            if (files.length === 0) {
                console.log('No files selected for upload');
            }
        });
    }
});
</script>
@endsection
