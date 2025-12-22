<?php
namespace App\Helpers;

class OrderStatusHelper
{
    // ===== CODE TRẠNG THÁI (Theo mô hình Shopee) =====
    const PENDING         = 'pending';         // Chờ xác nhận
    const PREPARING       = 'preparing';       // Chờ lấy hàng (người bán đã xác nhận, đang chuẩn bị)
    const SHIPPING        = 'shipping';        // Đang giao (đã giao cho đơn vị vận chuyển)
    const DELIVERED       = 'delivered';       // Đã giao (khách đã nhận được hàng)
    const COMPLETED       = 'completed';       // Hoàn thành (khách đã xác nhận nhận hàng)
    const CANCELLED       = 'cancelled';       // Đã hủy
    const REFUNDED        = 'refunded';        // Đã hoàn tiền (cũ)

    // ===== STATUS HIỂN THỊ TRONG DROPDOWN (Theo chuẩn Shopee) =====
    public static function getStatuses(): array
    {
        return [
            self::PENDING         => 'Chờ xác nhận',
            self::PREPARING       => 'Chờ lấy hàng',
            self::SHIPPING        => 'Đang giao',
            self::DELIVERED       => 'Đã giao',
            self::COMPLETED       => 'Hoàn thành',
            self::CANCELLED       => 'Đã hủy',
        ];
    }

    // ===== MAP TRẠNG THÁI CŨ =====
    public static function mapOldStatus(string $status): string
    {
        return match (mb_strtolower(trim($status))) {
            'cancel',
            'canceled',
            'cancelled',
            'đã hủy',
            'da_huy',
            'da huy',
            'huy'              => self::CANCELLED,

            'processing'       => self::PREPARING,
            'awaiting_payment' => self::PENDING,
            'awaiting_pickup'  => self::SHIPPING, // Chuyển sang Shipping
            'shipped'          => self::SHIPPING,
            'shipping'         => self::SHIPPING,

            self::PENDING,
            self::PREPARING,
            self::SHIPPING,
            self::DELIVERED,
            self::COMPLETED,
            self::CANCELLED,
            self::REFUNDED     => $status,

            default            => $status,
        };
    }

    // ===== TÊN HIỂN THỊ =====
    public static function getStatusName(string $status): string
    {
        $status = self::mapOldStatus($status);

        return self::getStatuses()[$status] ?? ($status === self::REFUNDED ? 'Đã hoàn tiền' : ucfirst($status));
    }

    // ===== BADGE =====
    public static function getStatusBadgeClass(string $status): string
    {
        return match (self::mapOldStatus($status)) {
            self::PENDING         => 'bg-warning text-dark',
            self::PREPARING       => 'bg-primary',
            self::SHIPPING        => 'bg-info',
            self::DELIVERED       => 'bg-success',
            self::COMPLETED       => 'bg-success',
            self::CANCELLED       => 'bg-danger',
            self::REFUNDED        => 'bg-secondary',
            default              => 'bg-secondary',
        };
    }

    // ===== KIỂM TRA ĐƯỢC UPDATE KHÔNG =====
    // Logic chuyển theo mô hình Shopee:
    // PENDING (Chờ xác nhận) 
    //   → PREPARING (Chờ lấy hàng) - Người bán xác nhận, chuẩn bị hàng
    //   → SHIPPING (Đang giao) - Đã giao cho đơn vị vận chuyển
    //   → DELIVERED (Đã giao) - Khách đã nhận được hàng
    //   → COMPLETED (Hoàn thành) - Khách xác nhận đã nhận hàng
    // Có thể hủy: CHỈ PENDING và PREPARING → CANCELLED
    // KHÔNG thể hủy: SHIPPING, DELIVERED, COMPLETED (đã giao cho đơn vị vận chuyển)
    public static function canUpdateStatus(string $currentStatus, string $newStatus): bool
    {
        $current = self::mapOldStatus($currentStatus);
        $new     = self::mapOldStatus($newStatus);

        // Trạng thái giống nhau thì không cần chuyển
        if ($current === $new) {
            return false;
        }

        // ĐÃ KẾT THÚC → CẤM ĐỔI (CANCELLED và COMPLETED không thể đổi)
        // DELIVERED có thể chuyển sang COMPLETED, nhưng COMPLETED không thể quay lại
        if (in_array($current, [
            self::CANCELLED,
            self::COMPLETED,
            self::REFUNDED,
        ], true)) {
            return false;
        }

        // Có thể hủy CHỈ ở PENDING và PREPARING (theo Shopee)
        // KHÔNG thể hủy khi đã giao cho đơn vị vận chuyển (SHIPPING, DELIVERED, COMPLETED)
        // Khi đã SHIPPING → chỉ có thể chuyển sang DELIVERED, không thể hủy
        if ($new === self::CANCELLED) {
            return in_array($current, [
                self::PENDING,   // Chờ xác nhận - có thể hủy
                self::PREPARING, // Chờ lấy hàng - có thể hủy (trước khi giao cho đơn vị vận chuyển)
                // SHIPPING không thể hủy vì đã giao cho đơn vị vận chuyển
            ], true);
        }

        // Chỉ có thể chuyển sang COMPLETED từ DELIVERED (khách xác nhận đã nhận hàng)
        if ($new === self::COMPLETED) {
            return $current === self::DELIVERED;
        }

        // Flow chuyển từng bước một (theo mô hình Shopee)
        // Có thể bỏ qua bước nhưng không thể quay lại
        $flow = [
            self::PENDING   => [
                self::PREPARING,  // Bước tiếp theo: Xác nhận đơn hàng
                self::CANCELLED,  // Hoặc hủy
            ],
            self::PREPARING => [
                self::SHIPPING,   // Bước tiếp theo: Giao cho đơn vị vận chuyển
                self::CANCELLED,  // Hoặc hủy (trước khi giao cho đơn vị vận chuyển)
            ],
            self::SHIPPING => [
                self::DELIVERED,  // CHỈ có thể chuyển sang DELIVERED
                // KHÔNG thể hủy khi đã giao cho đơn vị vận chuyển (theo Shopee)
            ],
            self::DELIVERED => [
                self::COMPLETED,  // Chỉ có thể chuyển sang COMPLETED (khách xác nhận)
            ],
        ];

        return in_array($new, $flow[$current] ?? [], true);
    }

    // ===== KIỂM TRA ĐƯỢC HỦY KHÔNG =====
    // Theo Shopee: Chỉ có thể hủy khi PENDING hoặc PREPARING
    // KHÔNG thể hủy khi đã SHIPPING (đã giao cho đơn vị vận chuyển)
    public static function canCancel(string $status): bool
    {
        return in_array(self::mapOldStatus($status), [
            self::PENDING,   // Chờ xác nhận - có thể hủy
            self::PREPARING, // Chờ lấy hàng - có thể hủy
            // SHIPPING không thể hủy vì đã giao cho đơn vị vận chuyển
        ], true);
    }
}
