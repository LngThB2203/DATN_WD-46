@extends('client.layouts.client')

@section('content')
<div class="container py-5">
    <h3>Giỏ hàng</h3>

    @if ($items->count() == 0)
        <p>Giỏ hàng trống.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->price) }} VNĐ</td>
                        <td>
                            <input class="form-control quantity-input" 
                                   data-id="{{ $item->id }}" 
                                   type="number" 
                                   min="1" 
                                   value="{{ $item->quantity }}">
                        </td>
                        <td>{{ number_format($item->quantity * $item->price) }} VNĐ</td>
                        <td>
                            <a href="{{ route('cart.remove', $item->id) }}" class="btn btn-danger btn-sm">Xóa</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Tổng tiền: {{ number_format($cart->total()) }} VNĐ</h4>

        <a href="{{ route('cart.clear') }}" class="btn btn-warning">Xóa toàn bộ giỏ</a>
        <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>

    @endif
</div>

<script>
    // Update quantity bằng AJAX
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function () {
            let id = this.dataset.id;
            let quantity = this.value;

            fetch(`/cart/update/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ quantity })
            });
        });
    });
</script>
@endsection
