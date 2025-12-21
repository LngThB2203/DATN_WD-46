<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background: #0d6efd; color: #ffffff; padding: 24px;">
                <h2 style="margin: 0;">Cảm ơn bạn đã đặt hàng!</h2>
                <p style="margin: 4px 0 0 0;">Đơn hàng #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px;">
                <p>Xin chào {{ $order->customer_name }},</p>
                <p>Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.</p>

                <h3 style="margin-top: 24px;">Thông tin đơn hàng</h3>
                <ul style="padding-left: 20px; margin: 8px 0;">
                    <li><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                    <li><strong>Phương thức thanh toán:</strong>
                        @if($order->payment_method === 'bank_transfer')
                            Chuyển khoản ngân hàng
                        @else
                            Thanh toán khi nhận hàng (COD)
                        @endif
                    </li>
                    <li><strong>Trạng thái:</strong> {{ \App\Helpers\OrderStatusHelper::getStatusName($order->order_status) }}</li>
                </ul>

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
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                                    {{ $item->product->name ?? 'Sản phẩm' }}
                                </td>
                                <td align="center" style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                                    {{ $item->quantity }}
                                </td>
                                <td align="right" style="padding: 8px; border-bottom: 1px solid #f1f1f1;">
                                    {{ number_format($item->subtotal, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3 style="margin-top: 24px;">Thanh toán</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0;">Tạm tính</td>
                        <td align="right" style="padding: 6px 0;">{{ number_format($order->subtotal, 0, ',', '.') }} đ</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0;">Giảm giá</td>
                        <td align="right" style="padding: 6px 0;">- {{ number_format($order->discount_total, 0, ',', '.') }} đ</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0;">Phí vận chuyển</td>
                        <td align="right" style="padding: 6px 0;">{{ number_format($order->shipping_cost, 0, ',', '.') }} đ</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: bold;">Tổng cộng</td>
                        <td align="right" style="padding: 6px 0; font-weight: bold;">{{ number_format($order->grand_total, 0, ',', '.') }} đ</td>
                    </tr>
                </table>

                <h3 style="margin-top: 24px;">Địa chỉ giao hàng</h3>
                <p style="margin: 4px 0;">{{ $order->shipping_address }}</p>
                @if($order->customer_note)
                    <p style="margin: 4px 0;"><strong>Ghi chú:</strong> {{ $order->customer_note }}</p>
                @endif

                @if($order->payment_method === 'bank_transfer')
                    <div style="margin-top: 24px; padding: 16px; background: #f1f7ff; border-radius: 6px;">
                        <h4 style="margin-top: 0;">Hướng dẫn chuyển khoản</h4>
                        <p style="margin: 6px 0;">Vui lòng chuyển khoản với nội dung: <strong>Thanh toán đơn hàng #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                        <ul style="padding-left: 20px; margin: 8px 0;">
                            <li>Ngân hàng: Vietcombank</li>
                            <li>Số tài khoản: 0123456789</li>
                            <li>Chủ tài khoản: Công ty TNHH ABC</li>
                        </ul>
                        <p style="margin: 6px 0;">Đơn hàng sẽ được xử lý ngay sau khi chúng tôi xác nhận thanh toán.</p>
                    </div>
                @endif

                <p style="margin-top: 24px;">Mọi thắc mắc vui lòng liên hệ hotline <strong>1900 0000</strong> hoặc reply email này.</p>
                <p style="margin-top: 16px; margin-bottom: 0;">Trân trọng,<br>Đội ngũ hỗ trợ khách hàng</p>
            </td>
        </tr>
    </table>
</body>
</html>

