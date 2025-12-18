<?php
 namespace App\Http\Controllers;

use App\Models\Product;

 class CompareController extends Controller{
    public function index(){
        $ids = session()->get('compare', []);
        $products = Product::whereIn('id', $ids)->get();
        return view('client.compare', compact('products'));
    }

    public function addToCompare($id)
{
    $product = Product::findOrFail($id);

    $compare = session()->get('compare', []);

    if (!in_array($id, $compare)) {
        if (count($compare) >= 4) {
            return redirect()->route('compare.index')->with('error', 'Chỉ so sánh tối đa 4 sản phẩm!');
        }
        $compare[] = $id;
        session()->put('compare', $compare);
    }

    // Chuyển thẳng sang trang compare
    return redirect()->route('compare.index')->with('success', 'Đã thêm vào danh sách so sánh!');
}


    public function remove($id)
    {
        $compare = session()->get('compare', []);
        $compare = array_filter($compare, function ($item) use ($id) {
            return $item != $id;
        });
        session()->put('compare', $compare);
        return back()->with('success', 'Đã xóa sản phẩm khỏi so sánh!');
    }

    public function clear()
    {
        session()->forget('compare');
        return back()->with('success', 'Đã xóa toàn bộ danh sách so sánh!');
    }
 }