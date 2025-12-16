<?php

namespace App\Helpers;

class OrderStatusHelper
{
    // Trạng thái đơn hàng theo mô hình Shopee
    const PENDING = 'pending';                    // Chờ xác nhận
    const CONFIRMED = 'confirmed';                 // Đã xác nhận
    const PREPARING = 'preparing';                 // Đang chuẩn bị hàng
    const AWAITING_PICKUP = 'awaiting_pickup';    // Chờ lấy hàng
    const SHIPPING = 'shipping';                   // Đang giao hàng
    const DELIVERED = 'delivered';                 // Đã giao hàng
    const COMPLETED = 'completed';                 // Hoàn thành
    const CANCELLED = 'cancelled';                 // Đã hủy
    const REFUNDED = 'refunded';                   // Đã hoàn tiền

    /**
     * Lấy danh sách trạng thái đơn hàng (theo mô hình Shopee)
     */
    public static function getStatuses(): array
    {
        return [
            self::PENDING => 'Chờ xác nhận',
<<<<<<< HEAD
            self::CONFIRMED => 'Đã xác nhận',
            self::PREPARING => 'Đang chuẩn bị hàng',
            self::AWAITING_PICKUP => 'Chờ lấy hàng',
            self::SHIPPING => 'Đang giao hàng',
            self::DELIVERED => 'Đã giao hàng',
            self::COMPLETED => 'Hoàn thành',
            self::CANCELLED => 'Đã hủy',
            self::REFUNDED => 'Đã hoàn tiền',
=======
            // Đã bỏ CONFIRMED (Đã xác nhận) theo yêu cầu
            self::PREPARING => 'Đang chuẩn bị hàng',
            self::AWAITING_PICKUP => 'Chờ lấy hàng',
            // Đã bỏ SHIPPING (Đang giao hàng) theo yêu cầu
            self::DELIVERED => 'Đã giao hàng',
            self::COMPLETED => 'Hoàn thành',
            self::CANCELLED => 'Đã hủy',
            // Đã bỏ trạng thái REFUNDED theo yêu cầu
>>>>>>> origin/main
        ];
    }

    /**
     * Lấy tên trạng thái bằng tiếng Việt
     */
    public static function getStatusName(string $status): string
    {
<<<<<<< HEAD
=======
        // Xử lý đặc biệt cho các trạng thái đã bỏ khỏi dropdown nhưng vẫn hiển thị nếu có đơn hàng cũ
        if ($status === self::REFUNDED) {
            return 'Đã hoàn tiền';
        }
        if ($status === self::CONFIRMED) {
            return 'Đã xác nhận';
        }
        if ($status === self::SHIPPING) {
            return 'Đang giao hàng';
        }
        
>>>>>>> origin/main
        // Map trạng thái cũ sang trạng thái mới trước khi lấy tên
        $mappedStatus = self::mapOldStatus($status);
        return self::getStatuses()[$mappedStatus] ?? $status;
    }

    /**
     * Lấy class CSS cho badge trạng thái (theo mô hình Shopee)
     */
    public static function getStatusBadgeClass(string $status): string
    {
        // Map trạng thái cũ sang trạng thái mới trước khi lấy class
        $mappedStatus = self::mapOldStatus($status);
        return match($mappedStatus) {
            self::PENDING => 'bg-warning text-dark',           // Chờ xác nhận - vàng
            self::CONFIRMED => 'bg-info',                       // Đã xác nhận - xanh dương nhạt
            self::PREPARING => 'bg-primary',                    // Đang chuẩn bị - xanh dương
            self::AWAITING_PICKUP => 'bg-info',                 // Chờ lấy hàng - xanh dương nhạt
            self::SHIPPING => 'bg-primary',                     // Đang giao hàng - xanh dương
            self::DELIVERED => 'bg-success',                    // Đã giao hàng - xanh lá
            self::COMPLETED => 'bg-success',                    // Hoàn thành - xanh lá
            self::CANCELLED => 'bg-danger',                    // Đã hủy - đỏ
            self::REFUNDED => 'bg-secondary',                   // Đã hoàn tiền - xám
            default => 'bg-secondary',
        };
    }

    /**
     * Map trạng thái cũ sang trạng thái mới (tương thích ngược)
     */
    public static function mapOldStatus(string $oldStatus): string
    {
        return match($oldStatus) {
<<<<<<< HEAD
            'processing' => self::CONFIRMED,        // processing -> Đã xác nhận
            'awaiting_payment' => self::PENDING,   // awaiting_payment -> Chờ xác nhận
            'shipped' => self::SHIPPING,           // shipped -> Đang giao hàng
=======
            'processing' => self::PREPARING,        // processing -> Đang chuẩn bị hàng (bỏ qua CONFIRMED)
            'awaiting_payment' => self::PENDING,   // awaiting_payment -> Chờ xác nhận
            'shipped' => self::DELIVERED,           // shipped -> Đã giao hàng (bỏ qua SHIPPING)
>>>>>>> origin/main
            default => $oldStatus,
        };
    }

    /**
     * Kiểm tra trạng thái có thể cập nhật không (theo mô hình Shopee)
     */
    public static function canUpdateStatus(string $currentStatus, string $newStatus): bool
    {
        // Map trạng thái cũ sang trạng thái mới
        $mappedCurrentStatus = self::mapOldStatus($currentStatus);
        
        // Nếu trạng thái mới cũng là trạng thái cũ, map nó luôn
        $mappedNewStatus = self::mapOldStatus($newStatus);
        
        // Không cho phép cập nhật nếu đã hủy, hoàn thành hoặc đã hoàn tiền
        if (in_array($mappedCurrentStatus, [self::CANCELLED, self::COMPLETED, self::REFUNDED])) {
            return false;
        }

        // Nếu đang ở trạng thái cũ (chưa map), cho phép chuyển sang trạng thái mới tương ứng
        if ($currentStatus !== $mappedCurrentStatus) {
            // Cho phép chuyển từ trạng thái cũ sang trạng thái mới tương ứng hoặc các trạng thái tiếp theo
            $allowedFromOldStatus = [
<<<<<<< HEAD
                'processing' => [self::CONFIRMED, self::PREPARING, self::CANCELLED],
                'awaiting_payment' => [self::PENDING, self::CONFIRMED, self::CANCELLED],
                'shipped' => [self::SHIPPING, self::DELIVERED, self::CANCELLED],
=======
                'processing' => [self::PREPARING, self::CANCELLED], // Bỏ qua CONFIRMED
                'awaiting_payment' => [self::PENDING, self::PREPARING, self::CANCELLED], // Bỏ qua CONFIRMED
                'shipped' => [self::DELIVERED, self::CANCELLED], // Bỏ qua SHIPPING
>>>>>>> origin/main
            ];
            
            if (isset($allowedFromOldStatus[$currentStatus])) {
                return in_array($mappedNewStatus, $allowedFromOldStatus[$currentStatus]);
            }
        }

        // Logic chuyển đổi trạng thái hợp lệ theo quy trình Shopee
        $allowedTransitions = [
<<<<<<< HEAD
            // Chờ xác nhận: có thể xác nhận, hủy
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            
            // Đã xác nhận: có thể chuẩn bị hàng, hủy
            self::CONFIRMED => [self::PREPARING, self::CANCELLED],
=======
            // Chờ xác nhận: có thể chuẩn bị hàng, hủy (bỏ qua CONFIRMED)
            self::PENDING => [self::PREPARING, self::CANCELLED],
>>>>>>> origin/main
            
            // Đang chuẩn bị hàng: có thể chờ lấy hàng, hủy
            self::PREPARING => [self::AWAITING_PICKUP, self::CANCELLED],
            
<<<<<<< HEAD
            // Chờ lấy hàng: có thể đang giao hàng, hủy
            self::AWAITING_PICKUP => [self::SHIPPING, self::CANCELLED],
            
            // Đang giao hàng: có thể đã giao hàng, hủy (trước khi giao)
            self::SHIPPING => [self::DELIVERED, self::CANCELLED],
=======
            // Chờ lấy hàng: có thể đã giao hàng, hủy (bỏ qua SHIPPING)
            self::AWAITING_PICKUP => [self::DELIVERED, self::CANCELLED],
>>>>>>> origin/main
            
            // Đã giao hàng: có thể hoàn thành
            self::DELIVERED => [self::COMPLETED],
        ];

        return in_array($mappedNewStatus, $allowedTransitions[$mappedCurrentStatus] ?? []);
    }
    
    /**
     * Kiểm tra trạng thái có thể hủy không
     */
    public static function canCancel(string $status): bool
    {
        // Chỉ có thể hủy ở các trạng thái trước khi giao hàng
        return in_array($status, [
            self::PENDING,
<<<<<<< HEAD
            self::CONFIRMED,
            self::PREPARING,
            self::AWAITING_PICKUP,
            self::SHIPPING, // Có thể hủy nếu chưa giao thực tế
=======
            self::PREPARING,
            self::AWAITING_PICKUP,
            // Đã bỏ CONFIRMED và SHIPPING theo yêu cầu
>>>>>>> origin/main
        ]);
    }
    
    /**
     * Lấy mô tả trạng thái (giống Shopee)
     */
    public static function getStatusDescription(string $status): string
    {
        return match($status) {
            self::PENDING => 'Đơn hàng đang chờ xác nhận. Bạn có thể hủy đơn trong thời gian này.',
            self::CONFIRMED => 'Đơn hàng đã được xác nhận. Người bán đang chuẩn bị hàng.',
            self::PREPARING => 'Người bán đang chuẩn bị hàng cho đơn của bạn.',
            self::AWAITING_PICKUP => 'Hàng đã sẵn sàng, đang chờ đơn vị vận chuyển đến lấy.',
            self::SHIPPING => 'Đơn hàng đang được vận chuyển đến bạn.',
            self::DELIVERED => 'Đơn hàng đã được giao thành công. Vui lòng kiểm tra và xác nhận.',
            self::COMPLETED => 'Đơn hàng đã hoàn tất. Cảm ơn bạn đã mua sắm!',
            self::CANCELLED => 'Đơn hàng đã bị hủy.',
            self::REFUNDED => 'Đơn hàng đã được hoàn tiền.',
            default => '',
        };
    }
}

