<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // Nếu chưa đăng nhập → đẩy về trang login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Nếu đăng nhập nhưng không phải admin → chặn
        if (Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        // Nếu là admin → cho vào tiếp
        return $next($request);
    }
}
