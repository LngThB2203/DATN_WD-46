<?php
namespace App\Http\Controllers\Admin;

use Storage;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller{
    public function index(Request $request)
    {
        $query = Post::with('category');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->orderByDesc('id')->paginate(10);
        return view('admin.post.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.post.create', compact('categories'));
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|max:255|unique:posts,title',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'content' => 'nullable',
    ]);

    $data = $request->only('title', 'content');

    $slug = Str::slug($request->title);
    $count = Post::where('slug', $slug)->count();
    $data['slug'] = $count ? $slug . '-' . ($count + 1) : $slug;

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('posts', 'public');
    }

    Post::create($data);

    return redirect()->route('post.index')->with('success', 'Thêm bài viết thành công!');
}


    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all();
        return view('admin.post.edit', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
{
    $post = Post::findOrFail($id);

    $request->validate([
        'title' => 'required|max:255|unique:posts,title,' . $post->id,
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'content' => 'nullable',
    ]);

    $data = $request->only('title', 'content');

    // Sinh slug duy nhất
    $slug = Str::slug($request->title);
    $count = Post::where('slug', $slug)->where('id', '!=', $post->id)->count();
    $data['slug'] = $count ? $slug . '-' . ($count + 1) : $slug;

    // Upload ảnh mới nếu có
    if ($request->hasFile('image')) {
        // Xóa ảnh cũ nếu có
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            \Storage::disk('public')->delete($post->image);
        }

        // Lưu ảnh mới
        $data['image'] = $request->file('image')->store('posts', 'public');
    }

    $post->update($data);

    return redirect()->route('post.index')->with('success', 'Cập nhật bài viết thành công!');
}


    public function destroy($id)
    {
        // Soft delete
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->route('post.index')->with('success', 'Bài viết đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->forceDelete();
        return redirect()->route('post.trashed')->with('success', 'Bài viết đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();
        return redirect()->route('post.trashed')->with('success', 'Bài viết đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $query = Post::onlyTrashed()->with('category');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->orderBy('deleted_at', 'desc')->paginate(10);
        return view('admin.post.trashed', compact('posts'));
    }

    
}