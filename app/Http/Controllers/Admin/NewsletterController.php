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
        // Soft delete
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->delete();

        return back()->with('success', 'Email đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $newsletter = Newsletter::withTrashed()->findOrFail($id);
        $newsletter->forceDelete();

        return redirect()->route('admin.newsletters.trashed')->with('success', 'Email đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $newsletter = Newsletter::withTrashed()->findOrFail($id);
        $newsletter->restore();

        return redirect()->route('admin.newsletters.trashed')->with('success', 'Email đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $search = $request->input('search');

        $newsletters = Newsletter::onlyTrashed()
            ->when($search, fn($q) => $q->where('email', 'like', "%{$search}%"))
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('admin.newsletters.trashed', compact('newsletters', 'search'));
    }
}
