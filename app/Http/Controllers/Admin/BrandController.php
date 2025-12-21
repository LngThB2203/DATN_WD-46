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
    // Kiểm tra sản phẩm trước khi xóa
    if ($brand->products()->exists()) {
        return redirect()->route('brand.index')
            ->with('error', 'Không thể xóa thương hiệu vì vẫn còn sản phẩm liên kết!');
    }

    $brand->delete();

    return redirect()->route('brand.index')
        ->with('success', 'Xóa thương hiệu thành công!');
}

    public function showProducts($id)
    {
        $brand = Brand::findOrFail($id);
        $products = Product::where('brand_id', $id)->latest()->get();
        return view('admin.brand.products', compact('brand', 'products'));
    }
}