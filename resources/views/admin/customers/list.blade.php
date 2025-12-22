@extends('admin.layouts.admin')

@section('title', 'Danh sách khách hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        <form method="GET" action="{{ route('admin.customers.list') }}" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control w-auto" placeholder="Tìm kiếm khách hàng...">
            <button class="btn btn-primary">Tìm</button>
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách khách hàng</h4>
                <a href="{{ route('admin.customers.export') }}" class="btn btn-sm btn-success">
                    + Xuất danh sách khách hàng
                </a>

            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover table-centered mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Quyền</th>
                            <th>Cấp độ</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($customers as $customer)
<tr>
    <td>{{ $customer->user->name }}</td>
    <td>{{ $customer->user->email }}</td>
    <td>{{ $customer->user->role }}</td>
    <td>{{ $customer->membership_level }}</td>
    <td>
        @if($customer->user->status == 1)
            <span class="badge bg-success">Hoạt động</span>
        @else
            <span class="badge bg-danger">Bị khóa</span>
        @endif
    </td>
    <td>
    <form action="{{ route('admin.customers.toggleUser', $customer->id) }}"
          method="POST"
          style="display:inline-block">
        @csrf
        @method('PATCH')

        @if($customer->user->status == 1)
            <button class="btn btn-sm btn-danger"
                    onclick="return confirm('Khóa tài khoản này?')">
                Khóa
            </button>
        @else
            <button class="btn btn-sm btn-success"
                    onclick="return confirm('Mở tài khoản này?')">
                Mở
            </button>
        @endif
    </form>
</td>
</tr>
@endforeach

                    </tbody>
                </table>
            </div>

            <div class="card-footer border-top">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
