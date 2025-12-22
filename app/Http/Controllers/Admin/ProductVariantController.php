<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantConcentration;
use App\Models\VariantScent;
use App\Models\VariantSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $variants = ProductVariant::with(['product', 'size', 'scent', 'concentration'])
            ->orderBy('product_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Lọc theo gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $variants = $query->orderBy('product_id', 'desc')->paginate(15)->withQueryString();

        return view('admin.variants.index', [
            'variants'       => $variants,
            'sizes'          => VariantSize::all(),
            'scents'         => VariantScent::all(),
            'concentrations' => VariantConcentration::all(),
        ]);
    }

    public function create(Request $request)
{
    // BẮT BUỘC có product_id
    if (!$request->filled('product_id')) {
        abort(404);
    }

    $product = Product::findOrFail($request->product_id);

    return view('admin.variants.create', [
        'product'        => $product,   
        'sizes'          => VariantSize::all(),
        'scents'         => VariantScent::all(),
        'concentrations' => VariantConcentration::all(),
    ]);
}
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'size_id'          => 'nullable|exists:variants_sizes,id',
            'scent_id'         => 'nullable|exists:variants_scents,id',
            'concentration_id' => 'nullable|exists:variants_concentrations,id',
            'sku'              => 'required|string|max:100|unique:product_variants,sku',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price_adjustment' => 'nullable|numeric',
            'gender'           => 'required|in:male,female,unisex',
        ]);

        $exists = ProductVariant::where([
            'product_id'       => $data['product_id'],
            'size_id'          => $data['size_id'],
            'scent_id'         => $data['scent_id'],
            'concentration_id' => $data['concentration_id'],
            'gender'           => $data['gender'],
        ])->exists();

        if ($exists) {
            return back()
                ->withErrors(['variant' => 'Biến thể này đã tồn tại'])
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        ProductVariant::create($data);

        return redirect()
    ->route('products.show', $data['product_id'])
    ->with('success', 'Tạo biến thể thành công!');

    }

    public function edit(ProductVariant $variant)
    {
        return view('admin.variants.edit', [
            'variant'        => $variant,
            'product'        => $variant->product,
            'products'       => Product::all(),
            'sizes'          => VariantSize::all(),
            'scents'         => VariantScent::all(),
            'concentrations' => VariantConcentration::all(),
        ]);
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'size_id'          => 'nullable|exists:variants_sizes,id',
            'scent_id'         => 'nullable|exists:variants_scents,id',
            'concentration_id' => 'nullable|exists:variants_concentrations,id',
            'sku'              => 'required|string|max:100|unique:product_variants,sku,' . $variant->id,
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price_adjustment' => 'nullable|numeric',
            'gender'           => 'required|in:male,female,unisex',
        ]);

        $exists = ProductVariant::where([
            'product_id'       => $data['product_id'],
            'size_id'          => $data['size_id'],
            'scent_id'         => $data['scent_id'],
            'concentration_id' => $data['concentration_id'],
            'gender'           => $data['gender'],
        ])
        ->where('id', '!=', $variant->id)
        ->exists();

        if ($exists) {
            return back()
                ->withErrors(['variant' => 'Biến thể này đã tồn tại'])
                ->withInput();
        }

        if ($request->hasFile('image')) {
            if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                Storage::disk('public')->delete($variant->image);
            }
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        $variant->update($data);

        return redirect()
    ->route('products.show', $data['product_id'])
    ->with('success', 'Sửa biến thể thành công!');

    }

    public function destroy(Request $request, ProductVariant $variant)
    {
        // Soft delete
        $variant->delete();

        return redirect()->route('variants.index')->with('success', 'Biến thể đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);

        if ($variant->image && Storage::disk('public')->exists($variant->image)) {
            Storage::disk('public')->delete($variant->image);
        }

        $variant->forceDelete();

        return redirect()->route('variants.trashed')->with('success', 'Biến thể đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);
        $variant->restore();

        return redirect()->route('variants.trashed')->with('success', 'Biến thể đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $query = ProductVariant::onlyTrashed()->with(['product', 'size', 'scent', 'concentration']);

        $variants = $query->orderBy('deleted_at', 'desc')->paginate(15);

        return redirect()->route('variants.index', ['page' => $request->input('page', 1)])
            ->with('success', 'Đã xóa biến thể!');
    }
}
