<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Báo cáo thống kê</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h1,h2 { margin: 4px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f3f4f6; }
  </style>
</head>
<body>
  <h1>Báo cáo thống kê</h1>
  <p>Khoảng thời gian: {{ $from }} đến {{ $to }}</p>

  <h2>Tổng quan</h2>
  <table>
    <tbody>
      <tr>
        <th>Tổng doanh thu</th>
        <td>{{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }} đ</td>
      </tr>
      <tr>
        <th>Số đơn hàng</th>
        <td>{{ $summary['orders'] ?? 0 }}</td>
      </tr>
      <tr>
        <th>Số sản phẩm bán</th>
        <td>{{ $summary['items'] ?? 0 }}</td>
      </tr>
    </tbody>
  </table>

  <h2>Top sản phẩm tháng {{ $month }}/{{ $year }}</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Doanh thu</th>
      </tr>
    </thead>
    <tbody>
      @foreach($top as $idx => $row)
      <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $row->product_name }}</td>
        <td>{{ (int) $row->total_qty }}</td>
        <td>{{ number_format((float) $row->total_amount, 0, ',', '.') }} đ</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
