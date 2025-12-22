<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->status == 0) {
            return redirect()->route('home')
                ->with('error', 'Tài khoản của bạn đã bị khóa');
        }

        return $next($request);
    }
}
