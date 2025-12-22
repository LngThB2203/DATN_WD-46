<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query()->with(['product', 'user'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }
        if ($request->filled('product')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->product.'%');
            });
        }

        $reviews = $query->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        $products = Product::select('id','name')->orderBy('name')->get();
        $users = User::select('id','name')->orderBy('name')->get();
        return view('admin.reviews.create', compact('products','users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'user_id' => ['nullable','exists:users,id'],
            'rating' => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string'],
            'status' => ['required','in:0,1'],
        ]);

        Review::create($data);

        return redirect()->route('admin.reviews.index')->with('success','Đã tạo đánh giá.');
    }

    public function edit(Review $review)
    {
        $products = Product::select('id','name')->orderBy('name')->get();
        $users = User::select('id','name')->orderBy('name')->get();
        return view('admin.reviews.edit', compact('review','products','users'));
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'user_id' => ['nullable','exists:users,id'],
            'rating' => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string'],
            'status' => ['required','in:0,1'],
        ]);

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('success','Đã cập nhật đánh giá.');
    }

    public function destroy(Review $review)
    {
        // Soft delete
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success','Đánh giá đã được xóa (có thể khôi phục).');
    }

    public function forceDelete($id)
    {
        $review = Review::withTrashed()->findOrFail($id);
        $review->forceDelete();
        return redirect()->route('admin.reviews.trashed')->with('success','Đánh giá đã được xóa vĩnh viễn.');
    }

    public function restore($id)
    {
        $review = Review::withTrashed()->findOrFail($id);
        $review->restore();
        return redirect()->route('admin.reviews.trashed')->with('success','Đánh giá đã được khôi phục.');
    }

    public function trashed(Request $request)
    {
        $query = Review::onlyTrashed()->with(['product', 'user']);

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }
        if ($request->filled('product')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->product.'%');
            });
        }

        $reviews = $query->orderBy('deleted_at', 'desc')->paginate(15);

        return view('admin.reviews.trashed', compact('reviews'));
    }

    public function toggleStatus(Review $review)
    {
        $review->status = $review->status ? 0 : 1;
        $review->save();
        return back()->with('success','Đã cập nhật trạng thái.');
    }
}
