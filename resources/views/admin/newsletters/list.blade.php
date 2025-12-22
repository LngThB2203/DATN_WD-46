@extends('admin.layouts.admin')

@section('title', 'Danh sách đăng ký nhận tin')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- Thanh tìm kiếm --}}
        <form method="GET" action="{{ route('admin.newsletters.list') }}" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control w-auto" placeholder="Tìm email...">
            <button class="btn btn-primary">Tìm</button>
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách email đăng ký nhận tin</h4>
                <a href="{{ route('admin.newsletters.send') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-send me-2"></i>Gửi Tin Newsletter
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover table-centered mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Ngày đăng ký</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newsletters as $n)
                            <tr>
                                <td>{{ $n->id }}</td>
                                <td>{{ $n->email }}</td>
                                <td>{{ $n->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.newsletters.delete', $n->id) }}" method="POST" onsubmit="return confirm('Xóa email này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-soft-danger">
                                            <i class="bi bi-trash"> Xóa</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có ai đăng ký</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-top">
                {{ $newsletters->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection
