<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // gui email xac thuc
      $user->sendEmailVerificationNotification();
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    // Đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');
        try {
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            }
        } catch (\RuntimeException $e) {
            $user = User::where('email', $request->input('email'))->first();
            if ($user) {
                $stored = $user->password;
                $plain = $request->input('password');
                $matched = false;
                if ($stored === $plain) {
                    $matched = true;
                } else {
                    $info = password_get_info($stored);
                    if (!empty($info['algo'])) {
                        $matched = password_verify($plain, $stored);
                    }
                }
                if ($matched) {
                    $user->password = $plain;
                    $user->save();
                    Auth::login($user, $remember);
                    $request->session()->regenerate();
                    return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
                }
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
    }
}
