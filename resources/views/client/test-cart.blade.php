@extends('client.layouts.client')

@section('title', 'Test Giỏ Hàng')

@section('content')
<div class="container py-5">
    <h2>Test Thêm Vào Giỏ Hàng</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <h4>Form Test</h4>
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Product ID</label>
                    <input type="number" name="product_id" class="form-control" value="2" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số lượng</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Giỏ hàng hiện tại</h4>
            @php
                $cart = session('cart', ['items' => []]);
            @endphp
            <pre>{{ print_r($cart, true) }}</pre>
            <a href="{{ route('cart.index') }}" class="btn btn-success">Xem giỏ hàng</a>
        </div>
    </div>
</div>
@endsection

