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
    /**
     * Danh sách biến thể
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'size', 'scent', 'concentration']);

        // Lọc theo gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $variants = $query
            ->orderBy('product_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.variants.index', [
            'variants'       => $variants,
            'sizes'          => VariantSize::all(),
            'scents'         => VariantScent::all(),
            'concentrations' => VariantConcentration::all(),
        ]);
    }

    /**
     * Form tạo biến thể (bắt buộc có product_id)
     */
    public function create(Request $request)
    {
        if (! $request->filled('product_id')) {
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

    /**
     * Lưu biến thể
     */
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

        // ==== CHECK TRÙNG (XỬ LÝ NULL ĐÚNG CÁCH) ====
        $query = ProductVariant::where('product_id', $data['product_id'])
            ->where('gender', $data['gender']);

        foreach (['size_id', 'scent_id', 'concentration_id'] as $field) {
            if (empty($data[$field])) {
                $query->whereNull($field);
            } else {
                $query->where($field, $data[$field]);
            }
        }

        if ($query->exists()) {
            return back()
                ->with('error', 'Biến thể này đã tồn tại')
                ->withInput();
        }

        // Upload ảnh
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        ProductVariant::create($data);

        return redirect()
            ->route('products.show', $data['product_id'])
            ->with('success', 'Tạo biến thể thành công!');
    }

    /**
     * Form sửa biến thể
     */
    public function edit(ProductVariant $variant)
    {
        return view('admin.variants.edit', [
            'variant'        => $variant,
            'product'        => $variant->product,
            'sizes'          => VariantSize::all(),
            'scents'         => VariantScent::all(),
            'concentrations' => VariantConcentration::all(),
        ]);
    }

    /**
     * Cập nhật biến thể
     */
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

        // ==== CHECK TRÙNG (TRỪ CHÍNH NÓ) ====
        $query = ProductVariant::where('product_id', $data['product_id'])
            ->where('gender', $data['gender'])
            ->where('id', '!=', $variant->id);

        foreach (['size_id', 'scent_id', 'concentration_id'] as $field) {
            if (empty($data[$field])) {
                $query->whereNull($field);
            } else {
                $query->where($field, $data[$field]);
            }
        }

        if ($query->exists()) {
            return back()
                ->with('error', 'Biến thể này đã tồn tại')
                ->withInput();
        }

        // Upload ảnh mới
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

    /**
     * Xóa mềm biến thể
     */
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return redirect()
            ->route('variants.index')
            ->with('success', 'Biến thể đã được xóa!');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);

        if ($variant->image && Storage::disk('public')->exists($variant->image)) {
            Storage::disk('public')->delete($variant->image);
        }

        $variant->forceDelete();

        return redirect()
            ->route('variants.trashed')
            ->with('success', 'Biến thể đã được xóa vĩnh viễn!');
    }

    /**
     * Khôi phục biến thể
     */
    public function restore($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);
        $variant->restore();

        return redirect()
            ->route('variants.trashed')
            ->with('success', 'Biến thể đã được khôi phục!');
    }
}
