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
<<<<<<< Updated upstream
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" {{ old('status') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">Active Status</label>
=======
<<<<<<< Updated upstream
                                                            <label for="product-weight" class="form-label">Weight</label>
                                                            <input type="text" id="product-weight" class="form-control" placeholder="In gm & kg">
                                                       </div>
                                                  </form>
                                             </div>
                                             <div class="col-lg-4">
                                                  <form>
                                                       <label for="gender" class="form-label">Gender</label>
                                                       <select class="form-control" id="gender" data-choices data-choices-groups data-placeholder="Select Gender">
                                                            <option value="">Select Gender</option>
                                                            <option value="Men">Men</option>
                                                            <option value="Women">Women</option>
                                                            <option value="Other">Other</option>
                                                       </select>
                                                  </form>
                                             </div>
                                        </div>
                                        <div class="row mb-4">
                                             <div class="col-lg-4">
                                                  <div class="mt-3">
                                                       <h5 class="text-dark fw-medium">Size :</h5>
                                                       <div class="d-flex flex-wrap gap-2" role="group" aria-label="Basic checkbox toggle button group">
                                                            <input type="checkbox" class="btn-check" id="size-xs1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-xs1">XS</label>

                                                            <input type="checkbox" class="btn-check" id="size-s1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-s1">S</label>

                                                            <input type="checkbox" class="btn-check" id="size-m1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-m1">M</label>

                                                            <input type="checkbox" class="btn-check" id="size-xl1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-xl1">Xl</label>

                                                            <input type="checkbox" class="btn-check" id="size-xxl1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-xxl1">XXL</label>
                                                            <input type="checkbox" class="btn-check" id="size-3xl1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="size-3xl1">3XL</label>

                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="col-lg-5">
                                                  <div class="mt-3">
                                                       <h5 class="text-dark fw-medium">Colors :</h5>
                                                       <div class="d-flex flex-wrap gap-2" role="group" aria-label="Basic checkbox toggle button group">
                                                            <input type="checkbox" class="btn-check" id="color-dark1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-dark1"> <i class="bx bxs-circle fs-18 text-dark"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-yellow1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-yellow1"> <i class="bx bxs-circle fs-18 text-warning"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-white1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-white1"> <i class="bx bxs-circle fs-18 text-white"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-red1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-red1"> <i class="bx bxs-circle fs-18 text-primary"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-green1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-green1"> <i class="bx bxs-circle fs-18 text-success"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-blue1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-blue1"> <i class="bx bxs-circle fs-18 text-danger"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-sky1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-sky1"> <i class="bx bxs-circle fs-18 text-info"></i></label>

                                                            <input type="checkbox" class="btn-check" id="color-gray1">
                                                            <label class="btn btn-light avatar-sm rounded d-flex justify-content-center align-items-center" for="color-gray1"> <i class="bx bxs-circle fs-18 text-secondary"></i></label>

=======
                                        <label for="brand" class="form-label">Brand</label>
                                        <input type="text" id="brand" name="brand" class="form-control @error('brand') is-invalid @enderror" 
                                               placeholder="Product Brand" value="{{ old('brand') }}">
                                        @error('brand')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                                       </div>
                                             </div>
                                        </div>
                                        <div class="row">
                                <div class="col-lg-6">
                                                       <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" {{ old('status') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">Active Status</label>
>>>>>>> Stashed changes
>>>>>>> Stashed changes
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
