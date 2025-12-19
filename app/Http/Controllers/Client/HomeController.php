<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['galleries', 'variants', 'variants.size', 'variants.scent', 'variants.concentration'])
            ->latest()
            ->paginate(8); 

        $heroBanner = Banner::latest('created_at')->first();
        $categories = Category::withCount('products')->take(4)->get();

        return view('client.home', compact('products', 'categories', 'heroBanner'));
    }

    // AJAX search
    public function search(Request $request)
    {
        $keyword  = $request->get('q', '');

        $products = Product::with(['galleries', 'variants.size', 'variants.scent'])
            ->where('name', 'like', "%$keyword%")
            ->take(8)
            ->get();

        $html = '';
        foreach ($products as $product) {
            $img = $product->galleries->where('is_primary', true)->first() ?? $product->galleries->first();
            $imgUrl = $img ? asset('storage/'.$img->image_path) : asset('assets/client/img/product/product-1.webp');

            $priceHtml = $product->sale_price
                ? '<span class="text-primary fw-bold">'.number_format($product->sale_price, 0, ',', '.').' VNĐ</span>
                   <span class="text-muted text-decoration-line-through ms-2">'.number_format($product->price, 0, ',', '.').' VNĐ</span>'
                : '<span class="text-primary fw-bold">'.number_format($product->price, 0, ',', '.').' VNĐ</span>';

            $variantHtml = '';
            if ($product->variants->count()) {
                $variantHtml .= '<select class="form-select variant-select mb-2" data-product-id="'.$product->id.'">';
                $variantHtml .= '<option value="">Chọn biến thể</option>';
                foreach ($product->variants as $variant) {
                    $variantHtml .= '<option value="'.$variant->id.'" data-price="'.$variant->price.'">'
                                    .($variant->size->size_name ?? '').' '.($variant->scent->scent_name ?? '').'</option>';
                }
                $variantHtml .= '</select>';
            }

            $html .= '
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <a href="'.route('product.show', $product->slug).'">
                        <img src="'.$imgUrl.'" class="card-img-top border" style="height:250px; object-fit:cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1">'.$product->name.'</h5>
                        '.$variantHtml.'
                        <div class="product-price mb-2">'.$priceHtml.'</div>
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="'.$product->id.'">Thêm vào giỏ</button>
                    </div>
                </div>
            </div>';
        }

        return response()->json(['html' => $html]);
    }
}
