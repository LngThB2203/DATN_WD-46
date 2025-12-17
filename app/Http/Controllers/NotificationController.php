<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PromotionNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // =========================
    // YÊU CẦU 2: GỬI KHUYẾN MÃI
    // =========================
    public function sendPromotion()
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->notify(
                new PromotionNotification(
                    'Khuyến mãi mới 🎉',
                    'Giảm 20% cho đơn hàng từ 500.000đ'
                )
            );
        }

        return back()->with('success', 'Đã gửi thông báo khuyến mãi');
    }

    // =========================
    // HIỂN THỊ THÔNG BÁO
    // =========================
    public function index()
    {
        $user = Auth::user(); // 👈 đổi từ auth() sang Auth

        $notifications = $user->notifications;

        return view('notifications.index', compact('notifications'));
    }
}
