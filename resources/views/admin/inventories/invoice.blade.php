<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Phiếu {{ $transaction->type }} #{{ $transaction->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width:100%; border-collapse: collapse; }
        .table th, .table td { border:1px solid #ddd; padding:8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Phiếu {{ ucfirst($transaction->type) }} hàng</h2>
        <p>Mã: #{{ $transaction->id }}</p>
    </div>

    <table class="table">
        <tr><th>Kho</th><td>{{ $transaction->warehouse->warehouse_name }}</td></tr>
        <tr><th>Sản phẩm</th><td>{{ $transaction->product->name }}</td></tr>
        <tr><th>Loại</th><td>{{ $transaction->type }}</td></tr>
        <tr><th>Số lượng</th><td>{{ $transaction->quantity }}</td></tr>
        <tr><th>Người thao tác</th><td>{{ $transaction->user->name ?? '—' }}</td></tr>
        <tr><th>Ghi chú</th><td>{{ $transaction->note }}</td></tr>
        <tr><th>Thời gian</th><td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>
</body>
</html>
