<?php
namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'galleries']);

        // Search by name or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sort
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products   = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.products.list', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Store request data:', [
            'name'         => $request->name,
            'has_images'   => $request->hasFile('images'),
            'has_image'    => $request->hasFile('image'),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'all_files'    => $request->allFiles(),
        ]);

        $request->validate([
            'name'        => 'required|string|max:200',
            'sku'         => 'nullable|string|max:100|unique:products,sku',
            'price'       => 'required|numeric|min:0',
            'sale_price'  => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create([
                'name'        => $request->name,
                'sku'         => $request->sku,
                'price'       => $request->price,
                'sale_price'  => $request->sale_price,
                'slug'        => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->has('status'),
            ]);

            if ($request->hasFile('images')) {
                $this->handleImageUpload($product, $request->file('images'));
            } elseif ($request->hasFile('image')) {
                $this->handleSingleImageUpload($product, $request->file('image'));
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success'    => true,
                    'message'    => 'Sản phẩm đã được tạo thành công!',
                    'product_id' => $product->id,
                ]);
            }

            return redirect()->route('products.index')->with('success', 'Sản phẩm đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Store error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Có lỗi xảy ra khi tạo sản phẩm: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'galleries']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('galleries');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'sku'         => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'price'       => 'required|numeric|min:0',
            'sale_price'  => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'name'        => $request->name,
                'sku'         => $request->sku,
                'price'       => $request->price,
                'sale_price'  => $request->sale_price,
                'slug'        => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->has('status'),
            ]);

            if ($request->hasFile('images')) {
                $this->handleImageUpload($product, $request->file('images'));
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            foreach ($product->galleries as $gallery) {
                if (Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
            }

            $product->delete();

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage());
        }
    }

    private function handleImageUpload(Product $product, array $images)
    {
        foreach ($images as $index => $image) {
            $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $path     = $image->storeAs('products', $filename, 'public');

            ProductGallery::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'alt_text'   => $product->name,
                'is_primary' => $index === 0,
            ]);
        }
    }

    private function handleSingleImageUpload(Product $product, $image)
    {
        $filename = time() . '_single.' . $image->getClientOriginalExtension();
        $path     = $image->storeAs('products', $filename, 'public');

        ProductGallery::create([
            'product_id' => $product->id,
            'image_path' => $path,
            'alt_text'   => $product->name,
            'is_primary' => true,
        ]);
    }

    public function deleteImage(ProductGallery $gallery)
    {
        if (Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();
        return response()->json(['success' => true, 'message' => 'Ảnh đã được xóa thành công!']);
    }

    public function setPrimaryImage(ProductGallery $gallery)
    {
        ProductGallery::where('product_id', $gallery->product_id)->update(['is_primary' => false]);
        $gallery->update(['is_primary' => true]);
        return response()->json(['success' => true, 'message' => 'Ảnh chính đã được cập nhật!']);
    }

    public function exportExcel(Request $request)
    {
        $query = Product::with(['category', 'galleries']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
    }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->get();

        return Excel::download(new ProductsExport($products), 'products_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Product::with(['category', 'galleries']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->get();

        $pdf = Pdf::loadView('admin.products.export-pdf', compact('products'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('products_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
