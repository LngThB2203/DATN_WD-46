<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .price {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DANH SÁCH SẢN PHẨM</h1>
        <p>Ngày xuất: {{ date('d/m/Y H:i:s') }}</p>
        <p>Tổng số sản phẩm: {{ $products->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">STT</th>
                <th style="width: 25%;">Tên sản phẩm</th>
                <th style="width: 10%;">SKU</th>
                <th style="width: 12%;">Giá gốc</th>
                <th style="width: 12%;">Giá khuyến mãi</th>
                <th style="width: 12%;">Danh mục</th>
                <th style="width: 12%;">Thương hiệu</th>
                <th style="width: 8%;">Trạng thái</th>
                <th style="width: 12%;">Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->sku ?? 'N/A' }}</td>
                <td class="price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                <td class="price">
                    @if($product->sale_price)
                        {{ number_format($product->sale_price, 0, ',', '.') }} VNĐ
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                <td>{{ $product->brand ?? 'N/A' }}</td>
                <td>
                    @if($product->status)
                        <span class="status-active">Hoạt động</span>
                    @else
                        <span class="status-inactive">Không hoạt động</span>
                    @endif
                </td>
                <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Báo cáo được tạo tự động bởi hệ thống quản lý sản phẩm</p>
    </div>
</body>
</html>
