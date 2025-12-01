<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $newsletters = Newsletter::query()
            ->when($search, fn($q) => $q->where('email', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.newsletters.list', compact('newsletters', 'search'));
    }

    public function destroy($id)
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->delete();

        return back()->with('success', 'Đã xóa email đăng ký thành công!');
    }
}
