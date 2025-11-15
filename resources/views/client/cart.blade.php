@extends('client.layouts.app')

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
                            <input type="number" class="quantity-input" data-id="{{ $item->id }}" value="{{ $item->quantity }}" min="1">

                        </td>
                        <td id="total-item-{{ $item->id }}">
    {{ number_format($item->quantity * $item->price) }} VND
</td>

                        <td>
                            <a href="{{ route('cart.remove', $item->id) }}" class="btn btn-danger btn-sm">Xóa</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Tổng tiền: <span id="total-cart">{{ number_format($total) }}</span> VND</h4>



        <a href="{{ route('cart.clear') }}" class="btn btn-warning">Xóa toàn bộ giỏ</a>
        <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>

    @endif
</div>
<script>
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function () {

        let itemId = this.dataset.id;
        let quantity = this.value;

        fetch("{{ route('cart.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: quantity
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // cập nhật giá từng sản phẩm
                document.getElementById("total-item-" + itemId).innerText =
                    new Intl.NumberFormat().format(data.item_total) + " VND";

                // cập nhật tổng giỏ
                document.getElementById("total-cart").innerText =
                    new Intl.NumberFormat().format(data.cart_total) + " VND";

                // cập nhật icon giỏ hàng
                loadCartCount();
            }
        });
    });
});
</script>

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
