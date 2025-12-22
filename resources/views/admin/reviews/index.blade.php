@extends('admin.layouts.admin')

@section('title', 'Danh sách đánh giá')

@section('content')
 <div class="page-content">
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
                         <h4 class="card-title flex-grow-1">Danh sách đánh giá</h4>
 
                         <a href="{{ route('admin.reviews.trashed') }}" class="btn btn-sm btn-outline-secondary">
                             <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                             Đã xóa
                         </a>
                         <a href="{{ route('admin.reviews.create') }}" class="btn btn-sm btn-primary">
                             + Thêm đánh giá
                         </a>
                     </div>
 
                     <div class="card-body border-bottom">
                         <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3 align-items-end">
                             <div class="col-md-6">
                                 <label class="form-label">Tìm theo tên sản phẩm</label>
                                 <input type="text" name="product" value="{{ request('product') }}" class="form-control" placeholder="Nhập tên sản phẩm...">
                             </div>
                             <div class="col-md-3">
                                 <label class="form-label">Trạng thái</label>
                                 <select name="status" class="form-select">
                                     <option value="">Tất cả trạng thái</option>
                                     <option value="1" @selected(request('status')==='1')>Đã duyệt</option>
                                     <option value="0" @selected(request('status')==='0')>Ẩn</option>
                                 </select>
                             </div>
                             <div class="col-md-3 d-flex gap-2">
                                 <button class="btn btn-primary w-100" type="submit">Lọc</button>
                                 <a class="btn btn-outline-secondary w-100" href="{{ route('admin.reviews.index') }}">Reset</a>
                             </div>
                         </form>
                     </div>
 
                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table align-middle table-hover mb-0">
                                 <thead class="bg-light-subtle">
                                     <tr>
                                         <th>ID</th>
                                         <th>Sản phẩm</th>
                                         <th>Người dùng</th>
                                         <th>Điểm</th>
                                         <th>Nội dung</th>
                                         <th>Trạng thái</th>
                                         <th>Thời gian</th>
                                         <th style="width: 220px">Hành động</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @forelse($reviews as $row)
                                         <tr>
                                             <td>{{ $row->id }}</td>
                                             <td>{{ $row->product->name ?? '-' }}</td>
                                             <td>{{ $row->user->name ?? 'Khách' }}</td>
                                             <td>{{ $row->rating }}/5</td>
                                             <td>{{ \Illuminate\Support\Str::limit($row->comment, 80) }}</td>
                                             <td>
                                                 <span class="badge {{ $row->status ? 'bg-success' : 'bg-secondary' }}">
                                                     {{ $row->status ? 'Đã duyệt' : 'Ẩn' }}
                                                 </span>
                                             </td>
                                             <td>
                                                 @if($row->created_at)
                                                     {{ $row->created_at->format('d/m/Y H:i') }}
                                                 @else
                                                     -
                                                 @endif
                                             </td>
                                             <td>
                                                 <div class="d-flex gap-2">
                                                     <form action="{{ route('admin.reviews.toggle', $row) }}" method="POST" class="d-inline">
                                                         @csrf
                                                         <button class="btn btn-sm btn-warning" type="submit">
                                                             {{ $row->status ? 'Ẩn' : 'Duyệt' }}
                                                         </button>
                                                     </form>
                                                     <a class="btn btn-sm btn-primary" href="{{ route('admin.reviews.edit', $row) }}">Sửa</a>
                                                     <form action="{{ route('admin.reviews.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Xoá đánh giá này?')">
                                                         @csrf
                                                         @method('DELETE')
                                                         <button class="btn btn-sm btn-danger" type="submit">Xoá</button>
                                                     </form>
                                                 </div>
                                             </td>
                                         </tr>
                                     @empty
                                         <tr>
                                             <td colspan="8" class="text-center text-muted">Không có dữ liệu</td>
                                         </tr>
                                     @endforelse
                                 </tbody>
                             </table>
                         </div>
                     </div>
 
                     <div class="card-footer border-top">
                         {{ $reviews->withQueryString()->links('pagination::bootstrap-5') }}
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
@endsection
