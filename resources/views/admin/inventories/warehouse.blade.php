@extends('admin.layouts.admin')

@section('title', 'Warehouse')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">All Warehouse List</h4>
                        </div>
                        <a href="{{ route('inventories.warehouse.add') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus"></i> Add Warehouse
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Warehouse Name</th>
                                    <th>Address</th>
                                    <th>Manager</th>
                                    <th>Phone</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $warehouse->id }}</td>
                                    <td>{{ $warehouse->warehouse_name }}</td>
                                    <td>{{ $warehouse->address }}</td>
                                    <td>
                                        @if($warehouse->manager)
                                            {{ $warehouse->manager->name }}
                                        @else
                                            <span class="text-muted fst-italic">No Manager</span>
                                        @endif
                                    </td>
                                    <td>{{ $warehouse->phone ?? '—' }}</td>
                                    <td>{{ $warehouse->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#" class="btn btn-light btn-sm" title="View">
                                                <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                            </a>
                                            <a href="#" class="btn btn-soft-primary btn-sm" title="Edit">
                                                <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                            </a>
                                            <form action="{{ route('inventories.warehouse.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-soft-danger btn-sm" title="Delete">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No warehouses found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer border-top">
                        {{ $warehouses->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
