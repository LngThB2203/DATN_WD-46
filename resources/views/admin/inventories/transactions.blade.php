@extends('admin.layouts.admin')

@section('content')
<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📦 <strong>Lịch sử nhập / xuất kho</strong></h5>
        </div>

        <div class="card-body">
            <form method="GET" class="row g-2 mb-4 p-3 bg-light rounded border">

                <div class="col-md-2">
                    <label class="small fw-bold">Loại giao dịch</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">-- Tất cả loại --</option>
                        <option value="import" @selected(request('type') == 'import')>Nhập kho</option>
                        <option value="export" @selected(request('type') == 'export')>Xuất kho</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold">Từ ngày</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold">Đến ngày</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm w-100 shadow-sm">
                        <i class="bi bi-filter"></i> Lọc dữ liệu
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle shadow-sm">
                    <thead class="table-light">
                        <tr class="text-center small text-uppercase fw-bold">
                            <th style="width: 150px;">Thời gian</th>

                            <th>Sản phẩm</th>
                            <th style="min-width: 200px;">Biến thể</th>
                            <th>Lô</th>
                            <th style="width: 100px;">Loại</th>
                            <th>Số lượng</th>
                            <th class="text-muted">Tồn trước</th>
                            <th class="fw-bold">Tồn sau</th>
                            <th>Tham chiếu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr class="text-center">
                            <td class="small">{{ $t->created_at->format('d-m-Y H:i:s') }}</td>
                
                            <td class="text-start fw-bold text-primary">{{ $t->product->name ?? '-' }}</td>
                            <td class="text-start">
                                @if($t->variant)
                                    <div class="d-flex flex-column gap-1">
                                        @if($t->variant->size)
                                            <span class="badge bg-secondary small">
                                                Kích thước: {{ $t->variant->size->size_name }}
                                            </span>
                                        @endif
                                        @if($t->variant->scent)
                                            <span class="badge bg-info small">
                                                Mùi hương: {{ $t->variant->scent->scent_name }}
                                            </span>
                                        @endif
                                        @if($t->variant->concentration)
                                            <span class="badge bg-warning text-dark small">
                                                Nồng độ: {{ $t->variant->concentration->concentration_name }}
                                            </span>
                                        @endif
                                        @if($t->variant->sku)
                                            <code class="small text-muted mt-1">{{ $t->variant->sku }}</code>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">Không có biến thể</span>
                                @endif
                            </td>
                            <td><span class="text-muted small">{{ $t->batch_code ?? '-' }}</span></td>
                            <td>
                                @if($t->type === 'import')
                                    <span class="badge bg-success-subtle text-success border border-success px-2">Nhập</span>
                                @elseif($t->type === 'export')
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2">Xuất</span>
                                @else
                                    <span class="badge bg-secondary px-2">{{ ucfirst($t->type) }}</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $t->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $t->quantity > 0 ? '+' : '' }}{{ $t->quantity }}
                            </td>
                            <td class="text-muted">{{ $t->before_quantity }}</td>
                            <td class="fw-bold text-dark bg-light">{{ $t->after_quantity }}</td>
                            <td>
                                @if($t->reference_type && $t->reference_id)
                                    <span class="badge bg-secondary small text-uppercase">
                                        {{ $t->reference_type }} #{{ $t->reference_id }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Không có lịch sử giao dịch nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-soft-info {
        background-color: #e1f5fe;
        color: #0288d1;
    }
    .table thead th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .page-content { padding: 20px; }
</style>
@endpush
