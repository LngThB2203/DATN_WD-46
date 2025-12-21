<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('parent')->paginate(10);
        $query      = Category::with('parent');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('category_name', 'like', "%{$search}%");
        }

        $categories = $query->orderByDesc('id')->paginate(10);

        $categories->appends(['search' => $request->search]);
        return view('admin.categories.list', compact('categories'));

    }

    public function create()
    {
        $parents = Category::all();
        return view('admin.categories.add', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|max:150',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path     = 'uploads/categories/';
            $file->move(public_path($path), $filename);

            $imagePath = $path . $filename;
        }

        Category::create([
            'category_name' => $request->category_name,
            'slug'          => Str::slug($request->category_name),
            'description'   => $request->description,
            'parent_id'     => $request->parent_id,
            'image'         => $imagePath,
        ]);

        return redirect()->route('admin.categories.list')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parents  = Category::where('id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|max:150',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $category = Category::findOrFail($id);
        $data     = [
            'category_name' => $request->category_name,
            'slug'          => Str::slug($request->category_name),
            'description'   => $request->description,
            'parent_id'     => $request->parent_id,
        ];

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $file     = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path     = 'uploads/categories/';
            $file->move(public_path($path), $filename);
            $data['image'] = $path . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.list')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        // Soft delete
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories.list')->with('success', 'Danh mục đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->forceDelete();
        return redirect()->route('admin.categories.trashed')->with('success', 'Danh mục đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('admin.categories.trashed')->with('success', 'Danh mục đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $query = Category::onlyTrashed()->with('parent');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('category_name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('deleted_at', 'desc')->paginate(10);
        $categories->appends($request->only('search'));
        
        return view('admin.categories.trashed', compact('categories'));
    }
    public function toggleStatus($id)
    {
        $category         = Category::findOrFail($id);
        $category->status = $category->status ? 0 : 1;
        $category->save();

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

}
