<?php
namespace App\Helpers;

class OrderStatusHelper
{
    // ===== CODE TRẠNG THÁI (Theo mô hình Shopee) =====
    const PENDING         = 'pending';         // Chờ xác nhận
    const PREPARING       = 'preparing';       // Đang chuẩn bị hàng
    const SHIPPING        = 'shipping';        // Đang giao hàng
    const DELIVERED       = 'delivered';       // Đã giao hàng
    const COMPLETED       = 'completed';       // Hoàn thành
    const CANCELLED       = 'cancelled';       // Đã hủy
    const REFUNDED        = 'refunded';        // Đã hoàn tiền (cũ)

    // ===== STATUS HIỂN THỊ TRONG DROPDOWN =====
    public static function getStatuses(): array
    {
        return [
            self::PENDING         => 'Chờ xác nhận',
            self::PREPARING       => 'Đang chuẩn bị hàng',
            self::SHIPPING        => 'Đang giao hàng',
            self::DELIVERED       => 'Đã giao hàng',
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
    // Logic chuyển theo mô hình Shopee: PENDING → PREPARING → SHIPPING → DELIVERED → COMPLETED
    // Hoặc hủy: PENDING/PREPARING/SHIPPING → CANCELLED
    public static function canUpdateStatus(string $currentStatus, string $newStatus): bool
    {
        $current = self::mapOldStatus($currentStatus);
        $new     = self::mapOldStatus($newStatus);

        // Trạng thái giống nhau thì không cần chuyển
        if ($current === $new) {
            return false;
        }

        // ĐÃ KẾT THÚC → CẤM ĐỔI (CANCELLED và COMPLETED không thể đổi)
        if (in_array($current, [
            self::CANCELLED,
            self::COMPLETED,
            self::REFUNDED,
        ], true)) {
            return false;
        }

        // Có thể hủy ở các trạng thái trước khi DELIVERED
        if ($new === self::CANCELLED) {
            return in_array($current, [
                self::PENDING,
                self::PREPARING,
                self::SHIPPING,
            ], true);
        }

        // Chỉ có thể chuyển sang COMPLETED từ DELIVERED
        if ($new === self::COMPLETED) {
            return $current === self::DELIVERED;
        }

        // Flow chuyển từng bước một (theo mô hình Shopee)
        $flow = [
            self::PENDING   => [
                self::PREPARING,  // Bước tiếp theo
                self::CANCELLED,  // Hoặc hủy
            ],
            self::PREPARING => [
                self::SHIPPING,   // Bước tiếp theo
                self::CANCELLED,  // Hoặc hủy
            ],
            self::SHIPPING => [
                self::DELIVERED,  // Bước tiếp theo
                self::CANCELLED,  // Hoặc hủy (trước khi giao)
            ],
            self::DELIVERED => [
                self::COMPLETED,  // Chỉ có thể chuyển sang COMPLETED
            ],
        ];

        return in_array($new, $flow[$current] ?? [], true);
    }

    // ===== KIỂM TRA ĐƯỢC HỦY KHÔNG =====
    public static function canCancel(string $status): bool
    {
        return in_array(self::mapOldStatus($status), [
            self::PENDING,
            self::PREPARING,
            self::SHIPPING,
        ], true);
    }
}
