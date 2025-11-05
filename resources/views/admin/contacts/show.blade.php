@extends('admin.layouts.admin')

@section('title', 'Chi tiết liên hệ')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Hiển thị thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Chi tiết liên hệ #{{ $contact->id }}</h4>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bx bx-arrow-back"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            {{-- Thông tin liên hệ --}}
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Thông tin liên hệ</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="150">Họ tên:</th>
                                                <td><strong>{{ $contact->name }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Email:</th>
                                                <td>
                                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                                </td>
                                            </tr>
                                            @if($contact->phone)
                                            <tr>
                                                <th>Điện thoại:</th>
                                                <td>
                                                    <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($contact->subject)
                                            <tr>
                                                <th>Chủ đề:</th>
                                                <td>{{ $contact->subject }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Trạng thái:</th>
                                                <td>
                                                    @if($contact->status === 'new')
                                                        <span class="badge bg-warning">Mới</span>
                                                    @elseif($contact->status === 'read')
                                                        <span class="badge bg-info">Đã đọc</span>
                                                    @elseif($contact->status === 'replied')
                                                        <span class="badge bg-success">Đã phản hồi</span>
                                                    @else
                                                        <span class="badge bg-secondary">Đã lưu trữ</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Ngày gửi:</th>
                                                <td>{{ $contact->created_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                            @if($contact->replied_at)
                                            <tr>
                                                <th>Ngày phản hồi:</th>
                                                <td>{{ $contact->replied_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Nội dung</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="bg-light p-3 rounded" style="white-space: pre-wrap;">{{ $contact->message }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quản lý --}}
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Quản lý trạng thái</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.contacts.update-status', $contact) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Trạng thái</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="new" {{ $contact->status === 'new' ? 'selected' : '' }}>Mới</option>
                                                    <option value="read" {{ $contact->status === 'read' ? 'selected' : '' }}>Đã đọc</option>
                                                    <option value="replied" {{ $contact->status === 'replied' ? 'selected' : '' }}>Đã phản hồi</option>
                                                    <option value="archived" {{ $contact->status === 'archived' ? 'selected' : '' }}>Đã lưu trữ</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bx bx-save"></i> Cập nhật
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Ghi chú</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.contacts.update-notes', $contact) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <textarea name="admin_notes" class="form-control" rows="6" 
                                                          placeholder="Thêm ghi chú về liên hệ này...">{{ $contact->admin_notes }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-secondary w-100">
                                                <i class="bx bx-save"></i> Lưu ghi chú
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="bx bx-trash"></i> Xóa liên hệ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

