<?php
namespace App\Helpers;

class OrderStatusHelper
{
                                               // ===== CODE TRẠNG THÁI =====
    const PENDING         = 'pending';         // Chờ xác nhận
    const PREPARING       = 'preparing';       // Đang chuẩn bị hàng
    const AWAITING_PICKUP = 'awaiting_pickup'; // Chờ lấy hàng
    const DELIVERED       = 'delivered';       // Đã giao
    const COMPLETED       = 'completed';       // Hoàn thành
    const CANCELLED       = 'cancelled';       // Đã hủy
    const REFUNDED        = 'refunded';        // Đã hoàn tiền (cũ)

    // ===== STATUS HIỂN THỊ TRONG DROPDOWN =====
    public static function getStatuses(): array
    {
        return [
            self::PENDING         => 'Chờ xác nhận',
            self::PREPARING       => 'Đang chuẩn bị hàng',
            self::AWAITING_PICKUP => 'Chờ lấy hàng',
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
            'shipped'          => self::DELIVERED,

            self::PENDING,
            self::PREPARING,
            self::AWAITING_PICKUP,
            self::DELIVERED,
            self::COMPLETED,
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
            self::AWAITING_PICKUP => 'bg-info',
            self::DELIVERED       => 'bg-success',
            self::COMPLETED       => 'bg-success',
            self::CANCELLED       => 'bg-danger',
            self::REFUNDED        => 'bg-secondary',
            default               => 'bg-secondary',
        };
    }

    // ===== KIỂM TRA ĐƯỢC UPDATE KHÔNG =====
    public static function canUpdateStatus(string $currentStatus, string $newStatus): bool
    {
        $current = self::mapOldStatus($currentStatus);
        $new     = self::mapOldStatus($newStatus);

        // ĐÃ KẾT THÚC → CẤM ĐỔI
        if (in_array($current, [
            self::CANCELLED,
            self::COMPLETED,
            self::DELIVERED,
            self::REFUNDED,
        ], true)) {
            return false;
        }

        $flow = [
            self::PENDING         => [
                self::PREPARING,
                self::CANCELLED,
            ],
            self::PREPARING       => [
                self::AWAITING_PICKUP,
                self::CANCELLED,
            ],
            self::AWAITING_PICKUP => [
                self::DELIVERED,
                self::CANCELLED,
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
            self::AWAITING_PICKUP,
        ], true);
    }
}
