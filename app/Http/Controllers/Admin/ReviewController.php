<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query()->with(['product', 'user'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->product_id);
        }

        if ($request->filled('date_from')) {
            $from = Carbon::parse($request->date_from)->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = Carbon::parse($request->date_to)->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        $reviews = $query->paginate(15)->withQueryString();
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function edit(Review $review)
    {
        abort(404);
    }

    public function update(Request $request, Review $review)
    {
        abort(404);
    }

    public function destroy(Review $review)
    {
        abort(404);
    }

    public function forceDelete($id)
    {
        abort(404);
    }

    public function restore($id)
    {
        abort(404);
    }

    public function trashed(Request $request)
    {
        abort(404);
    }

    public function show(Review $review)
    {
        $review->load(['product', 'user', 'order']);

        return view('admin.reviews.show', compact('review'));
    }

    public function toggleStatus(Review $review)
    {
        $review->status = $review->status ? 0 : 1;
        $review->save();
        return back()->with('success','Đã cập nhật trạng thái.');
    }
}
