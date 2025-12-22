<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 24px; margin: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 700px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        {{-- Header --}}
        <tr>
            <td style="background: linear-gradient(135deg, #0056b3 0%, #007bff 100%); color: #ffffff; padding: 32px 24px; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 16px;">✓</div>
                <h2 style="margin: 0; font-size: 24px; font-weight: bold;">Cảm ơn bạn đã đặt hàng!</h2>
                <p style="margin: 8px 0 0 0; font-size: 16px;">Đơn hàng #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</p>
            </td>
        </tr>

        {{-- Content --}}
        <tr>
            <td style="padding: 24px;">
                <p style="margin: 0 0 16px 0; font-size: 16px;">Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
                <p style="margin: 0 0 24px 0; color: #666; font-size: 14px;">Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.</p>

                {{-- Thông tin người nhận và đơn hàng --}}
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px; border-collapse: collapse;">
                    <tr>
                        <td width="50%" style="vertical-align: top; padding-right: 12px;">
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 6px; border-left: 4px solid #0056b3;">
                                <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #0056b3; font-weight: bold;">📋 Thông tin người nhận</h3>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Email:</strong> {{ $order->customer_email }}</p>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Địa chỉ giao hàng:</strong></p>
                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #333; padding-left: 16px;">{{ $order->shipping_address_line ?? $order->shipping_address ?? 'Chưa cập nhật' }}</p>
                                @if($order->customer_note)
                                <p style="margin: 8px 0 0 0; font-size: 14px;"><strong>Ghi chú:</strong> <em style="color: #666;">{{ $order->customer_note }}</em></p>
                                @endif
                            </div>
                        </td>
                        <td width="50%" style="vertical-align: top; padding-left: 12px;">
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 6px; border-left: 4px solid #0056b3;">
                                <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #0056b3; font-weight: bold;">📦 Thông tin đơn hàng</h3>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Mã đơn hàng:</strong> #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Trạng thái:</strong> 
                                    <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                        {{ \App\Helpers\OrderStatusHelper::getStatusName($order->order_status) }}
                                    </span>
                                </p>
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Phương thức thanh toán:</strong> 
                                    <span style="background: #17a2b8; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                        {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Thanh toán online (VNPay/MoMo)' }}
                                    </span>
                                </p>
                                @if($order->discount)
                                <p style="margin: 6px 0; font-size: 14px;"><strong>Mã giảm giá:</strong> 
                                    <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                        {{ $order->discount->code }}
                                    </span>
                                </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- Sản phẩm đã đặt --}}
                <h3 style="margin: 0 0 16px 0; font-size: 18px; color: #0056b3; font-weight: bold;">🛍️ Sản phẩm đã đặt</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 24px; border: 1px solid #dee2e6;">
                    <thead>
                        <tr style="background: #e9ecef;">
                            <th align="left" style="padding: 12px; border-bottom: 2px solid #dee2e6; font-size: 14px; width: 80px;">Hình ảnh</th>
                            <th align="left" style="padding: 12px; border-bottom: 2px solid #dee2e6; font-size: 14px;">Sản phẩm</th>
                            <th align="right" style="padding: 12px; border-bottom: 2px solid #dee2e6; font-size: 14px;">Đơn giá</th>
                            <th align="center" style="padding: 12px; border-bottom: 2px solid #dee2e6; font-size: 14px;">SL</th>
                            <th align="right" style="padding: 12px; border-bottom: 2px solid #dee2e6; font-size: 14px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $item)
                            @php
                                $product = $item->product;
                                $variant = $item->variant;
                                $primaryImage = $product->galleries->where('is_primary', true)->first() 
                                                 ?? $product->galleries->first();
                                $imageUrl = $primaryImage 
                                    ? asset('storage/' . $primaryImage->image_path)
                                    : asset('assets/client/img/product/default.jpg');
                            @endphp
                            <tr>
                                {{-- Hình ảnh --}}
                                <td style="padding: 12px; border-bottom: 1px solid #f1f1f1; vertical-align: middle;">
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $product->name ?? 'Sản phẩm' }}"
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6;"
                                         onerror="this.onerror=null; this.src='{{ asset('assets/client/img/product/default.jpg') }}';">
                                </td>
                                {{-- Thông tin sản phẩm --}}
                                <td style="padding: 12px; border-bottom: 1px solid #f1f1f1; vertical-align: top;">
                                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 8px; color: #333;">
                                        {{ $product->name ?? 'Sản phẩm' }}
                                    </div>
                                    @if($variant)
                                        <div style="margin-top: 4px;">
                                            @if($variant->size)
                                                <span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-right: 4px; display: inline-block; margin-bottom: 2px;">
                                                    Kích thước: {{ $variant->size->size_name }}
                                                </span>
                                            @endif
                                            @if($variant->scent)
                                                <span style="background: #0dcaf0; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-right: 4px; display: inline-block; margin-bottom: 2px;">
                                                    Mùi hương: {{ $variant->scent->scent_name }}
                                                </span>
                                            @endif
                                            @if($variant->concentration)
                                                <span style="background: #ffc107; color: black; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-right: 4px; display: inline-block; margin-bottom: 2px;">
                                                    Nồng độ: {{ $variant->concentration->concentration_name }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: #999; font-size: 12px;">Không có biến thể</span>
                                    @endif
                                </td>
                                {{-- Đơn giá --}}
                                <td align="right" style="padding: 12px; border-bottom: 1px solid #f1f1f1; vertical-align: middle; font-size: 14px;">
                                    {{ number_format($item->price, 0, ',', '.') }} đ
                                </td>
                                {{-- Số lượng --}}
                                <td align="center" style="padding: 12px; border-bottom: 1px solid #f1f1f1; vertical-align: middle;">
                                    <span style="background: #0d6efd; color: white; padding: 4px 10px; border-radius: 12px; font-size: 13px; font-weight: bold;">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                {{-- Thành tiền --}}
                                <td align="right" style="padding: 12px; border-bottom: 1px solid #f1f1f1; vertical-align: middle; font-weight: 600; font-size: 14px; color: #0056b3;">
                                    {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Tổng tiền đơn hàng --}}
                <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 24px;">
                    <h3 style="margin: 0 0 16px 0; font-size: 16px; color: #0056b3; font-weight: bold;">💰 Tổng tiền đơn hàng</h3>
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; font-size: 14px;">Tạm tính:</td>
                            <td align="right" style="padding: 8px 0; font-size: 14px; font-weight: 600;">{{ number_format($order->subtotal ?? 0, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-size: 14px;">Phí vận chuyển:</td>
                            <td align="right" style="padding: 8px 0; font-size: 14px; font-weight: 600;">{{ number_format($order->shipping_cost ?? $order->shipping_fee ?? 0, 0, ',', '.') }} đ</td>
                        </tr>
                        @if($order->discount_total > 0)
                        <tr>
                            <td style="padding: 8px 0; font-size: 14px;">
                                Giảm giá
                                @if($order->discount)
                                    (Mã: <strong>{{ $order->discount->code }}</strong>)
                                @endif:
                            </td>
                            <td align="right" style="padding: 8px 0; font-size: 14px; font-weight: 600; color: #dc3545;">
                                -{{ number_format($order->discount_total, 0, ',', '.') }} đ
                            </td>
                        </tr>
                        @endif
                        <tr style="border-top: 2px solid #dee2e6;">
                            <td style="padding: 12px 0 0 0; font-size: 18px; font-weight: bold; color: #0056b3;">Tổng cộng:</td>
                            <td align="right" style="padding: 12px 0 0 0; font-size: 18px; font-weight: bold; color: #0056b3;">
                                {{ number_format($order->grand_total ?? $order->total_amount, 0, ',', '.') }} đ
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Hướng dẫn thanh toán --}}
                @if($order->payment_method === 'bank_transfer' || $order->payment_method === 'online')
                    <div style="margin-top: 24px; padding: 16px; background: #e7f3ff; border-left: 4px solid #0056b3; border-radius: 6px;">
                        <h4 style="margin: 0 0 12px 0; font-size: 16px; color: #0056b3;">💳 Hướng dẫn thanh toán</h4>
                        @if($order->payment_method === 'bank_transfer')
                            <p style="margin: 8px 0; font-size: 14px;">Vui lòng chuyển khoản với nội dung: <strong>Thanh toán đơn hàng #{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                            <ul style="padding-left: 20px; margin: 8px 0; font-size: 14px;">
                                <li>Ngân hàng: Vietcombank</li>
                                <li>Số tài khoản: 0123456789</li>
                                <li>Chủ tài khoản: Công ty TNHH ABC</li>
                            </ul>
                            <p style="margin: 8px 0 0 0; font-size: 14px;">Đơn hàng sẽ được xử lý ngay sau khi chúng tôi xác nhận thanh toán.</p>
                        @else
                            <p style="margin: 8px 0; font-size: 14px;">Bạn sẽ được chuyển đến cổng thanh toán VNPay/MoMo để hoàn tất thanh toán.</p>
                        @endif
                    </div>
                @endif

                {{-- Footer --}}
                <p style="margin: 24px 0 16px 0; font-size: 14px; color: #666;">
                    Mọi thắc mắc vui lòng liên hệ hotline <strong style="color: #0056b3;">1900 0000</strong> hoặc reply email này.
                </p>
                <p style="margin: 16px 0 0 0; font-size: 14px; color: #666;">
                    Trân trọng,<br>
                    <strong>Đội ngũ hỗ trợ khách hàng<br>46 Perfume Shop</strong>
                </p>
            </td>
        </tr>

        {{-- Footer với logo/branding --}}
        <tr>
            <td style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; font-size: 12px; color: #999;">
                    © {{ date('Y') }} 46 Perfume Shop. Tất cả quyền được bảo lưu.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
