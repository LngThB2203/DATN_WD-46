@extends('admin.layouts.admin')

@section('title', 'Sửa đánh giá')

@section('content')
 <div class="page-content">
     <div class="container-fluid">
 
         @if($errors->any())
             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                 {{ $errors->first() }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
             </div>
         @endif
 
         <div class="row">
             <div class="col-xl-12">
                 <div class="card">
                     <div class="card-header d-flex justify-content-between align-items-center">
                         <h4 class="card-title mb-0">Sửa đánh giá #{{ $review->id }}</h4>
                         <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-secondary">
                             <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                             Quay lại
                         </a>
                     </div>
                     <div class="card-body">
                         <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                             @csrf
                             @method('PUT')
 
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                         <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                             @foreach($products as $p)
                                                 <option value="{{ $p->id }}" @selected(old('product_id', $review->product_id)==$p->id)>{{ $p->name }}</option>
                                             @endforeach
                                         </select>
                                         @error('product_id')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
 
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label class="form-label">Người dùng</label>
                                         <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                             <option value="">Khách</option>
                                             @foreach($users as $u)
                                                 <option value="{{ $u->id }}" @selected(old('user_id', $review->user_id)==$u->id)>{{ $u->name }}</option>
                                             @endforeach
                                         </select>
                                         @error('user_id')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
 
                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <label class="form-label">Điểm (1-5) <span class="text-danger">*</span></label>
                                         <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                             @for($i=1;$i<=5;$i++)
                                                 <option value="{{ $i }}" @selected(old('rating', $review->rating)==$i)>{{ $i }}</option>
                                             @endfor
                                         </select>
                                         @error('rating')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
 
                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                         <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                             <option value="1" @selected(old('status', (string)$review->status)==='1')>Đã duyệt</option>
                                             <option value="0" @selected(old('status', (string)$review->status)==='0')>Ẩn</option>
                                         </select>
                                         @error('status')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
 
                                 <div class="col-md-12">
                                     <div class="mb-3">
                                         <label class="form-label">Nội dung</label>
                                         <textarea name="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" placeholder="Nhận xét (tuỳ chọn)">{{ old('comment', $review->comment) }}</textarea>
                                         @error('comment')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
                             </div>
 
                             <div class="d-flex gap-2">
                                 <button class="btn btn-primary" type="submit">Cập nhật</button>
                                 <a class="btn btn-outline-secondary" href="{{ route('admin.reviews.index') }}">Hủy</a>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
@endsection
