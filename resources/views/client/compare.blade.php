@extends('client.layouts.app')

@section('title', 'So sánh sản phẩm')

@section('content')
<div class="container py-4">

    <h3 class="mb-4">So sánh sản phẩm</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(count($products) === 0)
        <div class="alert alert-info">Chưa có sản phẩm để so sánh.</div>
    @else

    <a href="{{ route('compare.clear') }}" class="btn btn-danger mb-3">
        Xóa tất cả
    </a>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
    <tr>
    <th>Ảnh</th>
    @foreach($products as $p)
        <td>
            @php
                $primary = $p->galleries->where('is_primary', true)->first() ?? $p->galleries->first();
                $img = $primary
                    ? asset('storage/'.$primary->image_path)
                    : ($p->image ? asset('storage/'.$p->image) : asset('assets/client/img/product/product-1.webp'));
            @endphp
            <img src="{{ $img }}" width="120" class="img-fluid rounded">
        </td>
    @endforeach
</tr>

    <tr>
        <th>Tên</th>
        @foreach($products as $p)
            <td>{{ $p->name }}</td>
        @endforeach
    </tr>
    <tr>
        <th>Giá</th>
        @foreach($products as $p)
            <td>{{ number_format($p->price) }}đ</td>
        @endforeach
    </tr>
    <tr>
    <th>Thương hiệu</th>
    @foreach($products as $p)
        <td>{{ $p->brand ?? '-' }}</td>
    @endforeach
</tr>
    <tr>
        <th>Hành động</th>
        @foreach($products as $p)
            <td>
                <a href="{{ route('compare.remove', $p->id) }}" class="btn btn-danger btn-sm">Xóa</a>
            </td>
        @endforeach
    </tr>
</table>

    </div>

    @endif
</div>
@endsection
