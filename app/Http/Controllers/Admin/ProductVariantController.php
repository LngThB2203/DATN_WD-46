<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use App\Models\VariantScent;
use App\Models\VariantConcentration;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with(['product', 'size', 'scent', 'concentration'])
            ->orderBy('product_id', 'desc')->paginate(15);
        return view('admin.variants.index', compact('variants'));
    }

    public function create()
    {
        $products = Product::all();
        $sizes = VariantSize::all();
        $scents = VariantScent::all();
        $concentrations = VariantConcentration::all();
        return view('admin.variants.create', compact('products', 'sizes', 'scents', 'concentrations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'nullable|exists:variants_sizes,id',
            'scent_id' => 'nullable|exists:variants_scents,id',
            'concentration_id' => 'nullable|exists:variants_concentrations,id',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'stock' => 'required|integer|min:0',
            'price_adjustment' => 'nullable|numeric',
            'gender' => 'required|in:male,female,unisex'
        ]);

        ProductVariant::create($data);
        return redirect()->route('variants.index')->with('success', 'Tạo biến thể thành công!');
    }

    public function edit(ProductVariant $variant)
    {
        $products = Product::all();
        $sizes = VariantSize::all();
        $scents = VariantScent::all();
        $concentrations = VariantConcentration::all();
        return view('admin.variants.edit', compact('variant', 'products', 'sizes', 'scents', 'concentrations'));
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'nullable|exists:variants_sizes,id',
            'scent_id' => 'nullable|exists:variants_scents,id',
            'concentration_id' => 'nullable|exists:variants_concentrations,id',
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variant->id,
            'stock' => 'required|integer|min:0',
            'price_adjustment' => 'nullable|numeric',
            'gender' => 'required|in:male,female,unisex'
        ]);

        $variant->update($data);
        return redirect()->route('variants.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return redirect()->route('variants.index')->with('success', 'Đã xóa biến thể!');
    }
}
