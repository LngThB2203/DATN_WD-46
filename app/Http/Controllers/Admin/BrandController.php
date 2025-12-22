<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = $request->only('name', 'origin', 'description');

        if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('brands', 'public');
    }
    Brand::create($data);

        return redirect()->route('brand.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = $request->only('name', 'origin', 'description');

        if ($request->hasFile('image')) {
        // Xóa ảnh cũ nếu có
        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }
        $data['image'] = $request->file('image')->store('brands', 'public');
    }
    $brand->update($data);

        return redirect()->route('brand.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    public function destroy(Brand $brand)
{
        // Soft delete
        $brand->delete();

        return redirect()->route('brand.index')
            ->with('success', 'Thương hiệu đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        // Kiểm tra sản phẩm trước khi xóa cứng
        if ($brand->products()->exists()) {
            return redirect()->route('brand.trashed')
                ->with('error', 'Không thể xóa vĩnh viễn thương hiệu vì vẫn còn sản phẩm liên kết!');
    }

        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->forceDelete();

        return redirect()->route('brand.trashed')
            ->with('success', 'Thương hiệu đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        $brand->restore();

        return redirect()->route('brand.trashed')
            ->with('success', 'Thương hiệu đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $query = Brand::onlyTrashed();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $brands = $query->orderBy('deleted_at', 'desc')->paginate(10);
        $brands->appends($request->only('search'));

        return view('admin.brand.trashed', compact('brands'));
}

    public function showProducts($id)
    {
        $brand = Brand::findOrFail($id);
        $products = Product::where('brand_id', $id)->latest()->get();
        return view('admin.brand.products', compact('brand', 'products'));
    }
}