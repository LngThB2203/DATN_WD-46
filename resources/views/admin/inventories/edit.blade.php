@extends('admin.layouts.admin')

@section('title', 'Edit Warehouse')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Edit Warehouse</h4>
                <a href="{{ route('inventories.warehouse') }}" class="btn btn-light btn-sm">
                    <i class="bx bx-left-arrow-alt"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('inventories.warehouse.update', $warehouse->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" name="warehouse_name" class="form-control" value="{{ old('warehouse_name', $warehouse->warehouse_name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $warehouse->address) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Manager</label>
                        <select name="manager_id" class="form-select">
                            <option value="">-- Select Manager --</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ $warehouse->manager_id == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $warehouse->phone) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update Warehouse</button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
