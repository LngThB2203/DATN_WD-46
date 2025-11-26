<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
class ClientBlogController extends Controller
{
    public function index(Request $request)
    {
         $query = Post::query();
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $posts = $query->latest()->paginate(6);
         $latestPosts = Post::whereDate('created_at', '>=', Carbon::now()->subDays(7))
                           ->latest()
                           ->limit(5)
                           ->get();
        return view('client.blog', compact('posts', 'latestPosts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $relatedPosts = Post::where('id', '!=', $post->id)->latest()->take(4)->get();
       $latestPosts = Post::whereDate('created_at', '>=', Carbon::now()->subDays(7))
                           ->latest()
                           ->limit(5)
                           ->get();
        return view('client.blog-details', compact('post', 'latestPosts', 'relatedPosts'));
    }
}
