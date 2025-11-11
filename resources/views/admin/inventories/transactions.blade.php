@extends('admin.layouts.admin')
@section('title','Lịch sử nhập/xuất')
@section('content')
<div class="page-content">
<div class="container-xxl">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Lịch sử nhập / xuất kho</h4>
        </div>

        <div class="card-body p-0">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>ID</th>
                        <th>Kho</th>
                        <th>Sản phẩm</th>
                        <th>Loại</th>
                        <th>Số lượng</th>
                        <th>Người thực hiện</th>
                        <th>Thời gian</th>
                        <th>Ghi chú</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->warehouse->warehouse_name ?? '—' }}</td>
                            <td>{{ $log->product->name ?? '—' }}</td>
                            <td>
                                @if($log->type === 'import')
                                    <span class="badge bg-success">Nhập</span>
                                @else
                                    <span class="badge bg-danger">Xuất</span>
                                @endif
                            </td>
                            <td>{{ $log->quantity }}</td>
                            <td>{{ $log->user->name ?? '—' }}</td>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->note ?? '—' }}</td>
                            <td>
                                <a href="{{ route('inventories.transactions.print', $log->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-printer"></i> In phiếu
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Chưa có giao dịch nhập/xuất nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer border-top">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div></div>
@endsection
