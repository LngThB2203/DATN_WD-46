<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request; // đúng Request
use App\Http\Controllers\Controller;

class ClientBlogController extends Controller
{
    // Trang danh sách blog + tìm kiếm
    public function index(Request $request)
    {
        $query = Post::query();

        // Lọc theo từ khóa search
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Lấy 6 bài viết/trang, mới nhất
        $posts = $query->latest()->paginate(6);

        return view('client.blog', compact('posts'));
    }

    // Trang chi tiết bài viết
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        // Optional: lấy 4 bài viết liên quan
        $relatedPosts = Post::where('id', '!=', $post->id)->latest()->take(4)->get();

        return view('client.blog-details', compact('post', 'relatedPosts'));
    }
}
