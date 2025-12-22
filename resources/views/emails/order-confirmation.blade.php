<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 24px;">
<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">

    <!-- HEADER -->
    <tr>
        <td style="background: #0d6efd; color: #ffffff; padding: 24px;">
            <h2 style="margin: 0;">Cảm ơn bạn đã đặt hàng!</h2>
            <p style="margin: 4px 0 0 0;">
                Đơn hàng #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding: 24px;">
            <p>Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
            <p>Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.</p>

            <!-- ORDER INFO -->
            <h3 style="margin-top: 24px;">Thông tin đơn hàng</h3>
            <!-- VIEW ORDER LINK -->
<div style="margin: 20px 0; text-align: center;">
    <a href="{{ url('/orders/' . $order->id) }}"
       style="
           display:inline-block;
           padding:12px 20px;
           background:#0d6efd;
           color:#ffffff;
           text-decoration:none;
           border-radius:6px;
           font-weight:bold;
       ">
        Xem chi tiết đơn hàng
    </a>
</div>

<p style="text-align:center; color:#666; font-size:13px;">
    Hoặc copy link:
    <br>
    <a href="{{ url('/orders/' . $order->id) }}">
        {{ url('/orders/' . $order->id) }}
    </a>
</p>

            <ul style="padding-left: 20px; margin: 8px 0;">
                <li><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                <li><strong>Phương thức thanh toán:</strong>
                    {{ $order->payment_method === 'online' ? 'Thanh toán online' : 'Thanh toán khi nhận hàng (COD)' }}
                </li>
                <li><strong>Trạng thái:</strong>
                    {{ \App\Helpers\OrderStatusHelper::getStatusName($order->order_status) }}
                </li>
            </ul>

            <!-- RECEIVER INFO -->
            <h3 style="margin-top: 24px;">Thông tin người nhận</h3>
            <ul style="padding-left: 20px; margin: 8px 0;">
                <li><strong>Họ tên:</strong> {{ $order->customer_name }}</li>
                <li><strong>Email:</strong> {{ $order->customer_email }}</li>
                <li><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</li>
                <li><strong>Địa chỉ:</strong> {{ $order->shipping_address_line }}</li>
            </ul>

            <!-- PRODUCTS -->
            <h3 style="margin-top: 24px;">Chi tiết sản phẩm</h3>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                <thead>
                <tr>
                    <th align="left" style="padding: 8px; border-bottom: 1px solid #ddd;">Sản phẩm</th>
                    <th align="center" style="padding: 8px; border-bottom: 1px solid #ddd;">SL</th>
                    <th align="right" style="padding: 8px; border-bottom: 1px solid #ddd;">Thành tiền</th>
                </tr>
                </thead>

                <tbody>
                @foreach($order->details as $item)
                    @php
                        $product = $item->product;
                        $variant = $item->variant;

                        $gallery = $product?->galleries?->first();
                        $imagePath = $gallery?->image_path;

                        $imageFile = $imagePath
                            ? storage_path('app/public/' . ltrim($imagePath, '/'))
                            : null;
                    @endphp

                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #f1f1f1;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="90" valign="top">
                                        @if($imageFile && file_exists($imageFile))
                                            <img
                                                src="{{ $message->embed($imageFile) }}"
                                                width="80"
                                                style="border-radius:6px; display:block;"
                                            >
                                        @endif
                                    </td>
                                    <td valign="top">
                                        <strong>{{ $product->name ?? 'Sản phẩm' }}</strong><br>
                                        <small style="color:#555;">
                                            Size: {{ $variant?->size?->size_name ?? 'N/A' }} |
                                            Mùi: {{ $variant?->scent?->scent_name ?? 'N/A' }} |
                                            Nồng độ: {{ $variant?->concentration?->concentration_name ?? 'N/A' }}
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td align="center" style="padding: 12px; border-bottom: 1px solid #f1f1f1;">
                            {{ $item->quantity }}
                        </td>

                        <td align="right" style="padding: 12px; border-bottom: 1px solid #f1f1f1;">
                            {{ number_format($item->subtotal, 0, ',', '.') }} đ
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- PAYMENT -->
            <h3 style="margin-top: 24px;">Thanh toán</h3>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td>Tạm tính</td>
                    <td align="right">{{ number_format($order->subtotal, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td>Giảm giá</td>
                    <td align="right">- {{ number_format($order->discount_total, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td>Phí vận chuyển</td>
                    <td align="right">{{ number_format($order->shipping_cost, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Tổng cộng</td>
                    <td align="right" style="font-weight:bold;">
                        {{ number_format($order->grand_total, 0, ',', '.') }} đ
                    </td>
                </tr>
            </table>

            <!-- SHOP INFO -->
            <hr style="margin:24px 0;">
            <p>
                <strong>Người gửi:</strong><br>
                Công ty TNHH ABC<br>
                Địa chỉ: 123 Nguyễn Văn A, Q.1, TP.HCM<br>
                Hotline: 1900 0000<br>
                Email: support@abc.com
            </p>

            <p style="margin-top: 16px;">Mọi thắc mắc vui lòng liên hệ hotline <strong>1900 0000</strong>.</p>
            <p>Trân trọng,<br><strong>Đội ngũ hỗ trợ khách hàng</strong></p>
        </td>
    </tr>
</table>
</body>
</html>
