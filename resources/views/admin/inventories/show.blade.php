@extends('admin.layouts.admin')

@section('title', 'Chi tiết tồn kho theo lô')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- Header --}}
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="mb-1">{{ $product->name }}</h4>

                @if($variant)
                    <div class="small text-muted">
                        SKU: {{ $variant->sku }}
                        @if($variant->size)
                            | Size: {{ $variant->size->size_name ?? $variant->size->name }}
                        @endif
                        @if($variant->scent)
                            | Mùi: {{ $variant->scent->scent_name ?? $variant->scent->name }}
                        @endif
                        @if($variant->concentration)
                            | Nồng độ: {{ $variant->concentration->concentration_name ?? $variant->concentration->name }}
                        @endif
                    </div>
                @else
                    <div class="small text-muted">Sản phẩm chính (không biến thể)</div>
                @endif

                <div class="mt-2">
                    <span class="badge bg-primary">
                        Tổng tồn: {{ number_format($totalQuantity) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Danh sách batch --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">
                    <i class="bi bi-layers me-1"></i> Danh sách lô hàng
                </h5>
                <a href="{{ route('inventories.received-orders') }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại tồn kho
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Kho</th>
                                <th>Mã lô</th>
                                <th>Hạn dùng</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $batch)
                                <tr>
                                    <td>{{ $batch->id }}</td>

                                    <td>
                                        <strong>{{ $batch->warehouse->warehouse_name }}</strong>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $batch->batch_code }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($batch->expired_at)
                                            {{ \Carbon\Carbon::parse($batch->expired_at)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <strong class="text-primary">
                                            {{ number_format($batch->quantity) }}
                                        </strong>
                                    </td>

                                    <td>
                                        @php
                                            $isLow = $batch->quantity <= 10;
                                        @endphp

                                        @if($isLow)
                                            <span class="badge bg-danger">Sắp hết</span>
                                        @else
                                            <span class="badge bg-success">Còn hàng</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">
                                            Không có lô hàng nào
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $batches->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
