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
    public function index()
    {
        $products = Product::with(['category', 'galleries'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.products.list', compact('products'));
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
        \Log::info('Store request data:', [
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

            \Log::info('Product created:', ['product_id' => $product->id]);

            // Handle image upload
            if ($request->hasFile('images')) {
                \Log::info('Processing multiple images...');
                $this->handleImageUpload($product, $request->file('images'));
            } elseif ($request->hasFile('image')) {
                \Log::info('Processing single image...');
                $this->handleSingleImageUpload($product, $request->file('image'));
            } else {
                \Log::info('No images to upload');
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
            \Log::error('Store error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
        \Log::info('handleImageUpload called', ['product_id' => $product->id, 'images_count' => count($images)]);
        
        foreach ($images as $index => $image) {
            \Log::info('Processing image', [
                'index' => $index,
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);
            
            try {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, 'public');
                
                \Log::info('Image stored', ['path' => $path]);

                ProductGallery::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $index === 0, // First image is primary
                ]);
                
                \Log::info('Gallery record created');
            } catch (\Exception $e) {
                \Log::error('Image upload error', ['error' => $e->getMessage()]);
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
        \Log::info('handleSingleImageUpload called', [
            'product_id' => $product->id,
            'original_name' => $image->getClientOriginalName(),
            'size' => $image->getSize(),
            'mime_type' => $image->getMimeType()
        ]);

        try {
            $filename = time() . '_single.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $filename, 'public');
            \Log::info('Single image stored', ['path' => $path]);

            ProductGallery::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'alt_text' => $product->name,
                'is_primary' => true,
            ]);
            \Log::info('Single gallery record created');
        } catch (\Exception $e) {
            \Log::error('Single image upload error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
