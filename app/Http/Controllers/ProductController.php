<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
            $query->where(function($q) use ($search) {
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
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(10)->withQueryString();
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
        // Debug request data
        \Illuminate\Support\Facades\Log::info('Store request data:', [
            'name' => $request->name,
            'has_images' => $request->hasFile('images'),
            'has_image' => $request->hasFile('image'),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'all_files' => $request->allFiles()
        ]);

        $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Create product
            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->has('status'),
            ]);

            \Illuminate\Support\Facades\Log::info('Product created:', ['product_id' => $product->id]);

            // Handle image upload
            if ($request->hasFile('images')) {
                \Illuminate\Support\Facades\Log::info('Processing multiple images...');
                $this->handleImageUpload($product, $request->file('images'));
            } elseif ($request->hasFile('image')) {
                \Illuminate\Support\Facades\Log::info('Processing single image...');
                $this->handleSingleImageUpload($product, $request->file('image'));
            } else {
                \Illuminate\Support\Facades\Log::info('No images to upload');
            }

            DB::commit();
            
            // Check if it's an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được tạo thành công!',
                    'product_id' => $product->id
                ]);
            }
            
            return redirect()->route('products.index')->with('success', 'Sản phẩm đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Store error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Có lỗi xảy ra khi tạo sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'galleries']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('galleries');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Update product
            $product->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->has('status'),
            ]);

            // Handle new image upload
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete product images
            foreach ($product->galleries as $gallery) {
                if (Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
            }

            // Delete product
            $product->delete();

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Handle image upload for product
     */
    private function handleImageUpload(Product $product, array $images)
    {
        \Illuminate\Support\Facades\Log::info('handleImageUpload called', ['product_id' => $product->id, 'images_count' => count($images)]);
        
        foreach ($images as $index => $image) {
            \Illuminate\Support\Facades\Log::info('Processing image', [
                'index' => $index,
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);
            
            try {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, 'public');
                
                \Illuminate\Support\Facades\Log::info('Image stored', ['path' => $path]);

                ProductGallery::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $index === 0, // First image is primary
                ]);
                
                \Illuminate\Support\Facades\Log::info('Gallery record created');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Image upload error', ['error' => $e->getMessage()]);
                throw $e;
            }
        }
    }

    /**
     * Delete product image
     */
    public function deleteImage(ProductGallery $gallery)
    {
        try {
            if (Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            
            $gallery->delete();
            
            return response()->json(['success' => true, 'message' => 'Ảnh đã được xóa thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa ảnh: ' . $e->getMessage()]);
        }
    }

    /**
     * Set primary image
     */
    public function setPrimaryImage(ProductGallery $gallery)
    {
        try {
            // Remove primary from all images of this product
            ProductGallery::where('product_id', $gallery->product_id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $gallery->update(['is_primary' => true]);

            return response()->json(['success' => true, 'message' => 'Ảnh chính đã được cập nhật!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle single image upload
     */
    private function handleSingleImageUpload(Product $product, $image)
    {
        \Illuminate\Support\Facades\Log::info('handleSingleImageUpload called', [
            'product_id' => $product->id,
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime_type' => $image->getMimeType()
        ]);

        try {
            $filename = time() . '_single.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $filename, 'public');
            \Illuminate\Support\Facades\Log::info('Single image stored', ['path' => $path]);

            ProductGallery::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'alt_text' => $product->name,
                'is_primary' => true,
            ]);
            \Illuminate\Support\Facades\Log::info('Single gallery record created');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Single image upload error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
