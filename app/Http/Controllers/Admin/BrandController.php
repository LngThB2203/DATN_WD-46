<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;

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
        ]);

        Brand::create($request->only('name', 'origin', 'description'));

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
        ]);

        $brand->update($request->only('name', 'origin', 'description'));

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
