<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Hiển thị danh sách banner
    public function index()
    {
        $banners = Banner::latest()->paginate(10); // Pagination
        return view('admin.banner.index', compact('banners'));
    }

    // Form thêm mới
    public function create()
    {
        return view('admin.banner.create');
    }

    // Lưu banner mới
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|url|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $path = $request->file('image')->store('uploads/banners', 'public');

        Banner::create([
            'image' => $path,
            'link' => $request->link,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('banner.index')->with('success', 'Thêm banner thành công!');
    }

    // Form sửa banner
    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    // Cập nhật banner
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link' => 'nullable|url|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->only(['link', 'start_date', 'end_date']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('uploads/banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('banner.index')->with('success', 'Cập nhật banner thành công!');
    }

    public function toggleStatus(Banner $banner)
{
    $banner->status = !$banner->status; // Đảo trạng thái
    $banner->save();

    return redirect()->route('banner.index')->with('success', 'Cập nhật trạng thái banner thành công!');
}


    // Xóa banner
    public function destroy(Banner $banner)
    {
        // Soft delete
        $banner->delete();

        return redirect()->route('banner.index')->with('success', 'Banner đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $banner = Banner::withTrashed()->findOrFail($id);

        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->forceDelete();

        return redirect()->route('banner.trashed')->with('success', 'Banner đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $banner = Banner::withTrashed()->findOrFail($id);
        $banner->restore();

        return redirect()->route('banner.trashed')->with('success', 'Banner đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $banners = Banner::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('admin.banner.trashed', compact('banners'));
    }
}
