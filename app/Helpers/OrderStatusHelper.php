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

    // ===== STATUS HIỂN THỊ TRONG DROPDOWN (Theo tên gọi của Shopee) =====
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
        $normalized = mb_strtolower(trim($status));
        
        return match ($normalized) {
            // Hủy đơn
            'cancel',
            'canceled',
            'cancelled',
            'đã hủy',
            'da_huy',
            'da huy',
            'huy'              => self::CANCELLED,

            // Trạng thái cũ
            'processing'       => self::PREPARING,
            'awaiting_payment' => self::PENDING,
            'awaiting_pickup'  => self::SHIPPING,
            'shipped'          => self::SHIPPING,

            // Trạng thái chuẩn (so sánh lowercase)
            'pending'          => self::PENDING,
            'preparing'        => self::PREPARING,
            'shipping'         => self::SHIPPING,
            'delivered'        => self::DELIVERED,
            'completed'        => self::COMPLETED,
            'refunded'         => self::REFUNDED,

            default            => $status, // Giữ nguyên nếu không match
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

        // HỦY ĐƠN HÀNG (theo chuẩn TMDT: Shopee, Tiki, Lazada)
        // Chỉ có thể hủy ở: PENDING (Chờ xác nhận)
        // KHÔNG thể hủy khi: PREPARING (Đang chuẩn bị hàng - đã bắt đầu xử lý), SHIPPING, DELIVERED, COMPLETED
        // Lý do: Khi đã PREPARING, hệ thống đã bắt đầu xử lý hàng (trừ kho), không thể hủy nữa
        if ($new === self::CANCELLED) {
            return $current === self::PENDING; // Chỉ cho phép hủy khi còn ở trạng thái chờ xác nhận
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
                self::SHIPPING,   // Bước tiếp theo: giao cho đơn vị vận chuyển
                // KHÔNG cho phép hủy khi đã PREPARING - đã bắt đầu xử lý hàng
            ],
            self::SHIPPING => [
                self::DELIVERED,  // Chỉ có thể chuyển sang DELIVERED (không thể hủy nữa)
                // KHÔNG cho phép hủy khi SHIPPING - hàng đã được bàn giao cho đơn vị vận chuyển
            ],
            self::DELIVERED => [
                self::COMPLETED,  // Chỉ có thể chuyển sang COMPLETED
            ],
        ];

        return in_array($new, $flow[$current] ?? [], true);
    }

    // ===== KIỂM TRA ĐƯỢC HỦY KHÔNG (Cho khách hàng) =====
    // Theo Shopee: Khách hàng chỉ có thể tự hủy ở trạng thái "Chờ xác nhận" (PENDING)
    // Khi đã "Đang chuẩn bị hàng" (PREPARING) hoặc "Đang giao" (SHIPPING), khách phải liên hệ shop để hủy
    public static function canCancel(string $status): bool
    {
        $mappedStatus = self::mapOldStatus($status);
        
        // Khách hàng chỉ có thể hủy ở: PENDING
        // KHÔNG cho phép hủy khi: PREPARING, SHIPPING, DELIVERED, COMPLETED, CANCELLED, REFUNDED
        return $mappedStatus === self::PENDING;
    }
    
    // ===== KIỂM TRA ADMIN CÓ THỂ HỦY KHÔNG =====
    // Theo chuẩn TMDT: Admin chỉ có thể hủy ở trạng thái "Chờ xác nhận" (PENDING)
    // KHÔNG được hủy khi đã "Đang chuẩn bị hàng" (PREPARING) - vì đã bắt đầu xử lý hàng
    public static function canAdminCancel(string $status): bool
    {
        $mappedStatus = self::mapOldStatus($status);
        
        // Admin chỉ có thể hủy ở: PENDING (Chờ xác nhận)
        // KHÔNG cho phép hủy khi: PREPARING (đã bắt đầu xử lý), SHIPPING, DELIVERED, COMPLETED, CANCELLED, REFUNDED
        return $mappedStatus === self::PENDING;
    }
}
