<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu {{ $transaction->type === 'import' ? 'Nhập kho' : 'Xuất kho' }}</title>

    <style>
        /* 👇 Dán đoạn này vào đây */
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/DejaVuSans-Bold.ttf') }}') format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        strong { font-weight: bold; }
    </style>
</head>

<body>
    <h2>Phiếu {{ $transaction->type === 'import' ? 'Nhập kho' : 'Xuất kho' }}</h2>

    <p><strong>Kho:</strong> {{ $transaction->warehouse->warehouse_name }}</p>
    <p><strong>Sản phẩm:</strong> {{ $transaction->product->name }}</p>
    <p><strong>Số lượng:</strong> {{ $transaction->quantity }}</p>
    <p><strong>Người thực hiện:</strong> {{ $transaction->user->name ?? '—' }}</p>
    <p><strong>Ngày:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Ghi chú:</strong> {{ $transaction->note ?? 'Không có' }}</p>

    <br><br>
    <table width="100%">
        <tr>
            <td style="text-align:center;">
                <strong>Người lập phiếu</strong><br>
                (Ký và ghi rõ họ tên)
            </td>
            <td style="text-align:center;">
                <strong>Người duyệt</strong><br>
                (Ký và ghi rõ họ tên)
            </td>
        </tr>
    </table>
</body>
</html>
