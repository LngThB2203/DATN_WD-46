<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Mail\NewsletterThanks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email'
        ]);

        $newsletter = Newsletter::create([
            'email' => $request->email,
        ]);

        try {
            Mail::to($newsletter->email)->send(new NewsletterThanks());
        } catch (\Exception $e) {
            // Nếu chưa cấu hình mail thì chỉ bỏ qua
        }

        return back()->with('success', 'Cảm ơn bạn đã đăng ký nhận tin!');
    }
}
