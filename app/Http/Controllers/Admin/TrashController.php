<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Models\Newsletter;
use App\Models\Contact;
use App\Models\Warehouse;
use App\Models\Discount;
use App\Models\Post;
use App\Models\Banner;
use App\Models\Review;
use App\Models\Customer;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search', '');

        $allItems = collect();

        // Lấy tất cả các items đã xóa mềm từ các modules
        if ($type === 'all' || $type === 'products') {
            $products = Product::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                })
                ->with(['category', 'brand'])
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'product',
                        'type_label' => 'Sản phẩm',
                        'name' => $item->name,
                        'description' => $item->sku,
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('products.restore', $item->id),
                        'force_delete_route' => route('products.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($products);
        }

        if ($type === 'all' || $type === 'categories') {
            $categories = Category::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('category_name', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'category',
                        'type_label' => 'Danh mục',
                        'name' => $item->category_name,
                        'description' => $item->description,
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('admin.categories.restore', $item->id),
                        'force_delete_route' => route('admin.categories.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($categories);
        }

        if ($type === 'all' || $type === 'brands') {
            $brands = Brand::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'brand',
                        'type_label' => 'Thương hiệu',
                        'name' => $item->name,
                        'description' => $item->origin ?? '',
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('brand.restore', $item->id),
                        'force_delete_route' => route('brand.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($brands);
        }

        if ($type === 'all' || $type === 'variants') {
            $variants = ProductVariant::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('sku', 'like', "%{$search}%");
                })
                ->with(['product', 'size', 'scent', 'concentration'])
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'variant',
                        'type_label' => 'Biến thể',
                        'name' => $item->product ? $item->product->name : 'N/A',
                        'description' => 'SKU: ' . $item->sku,
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('variants.restore', $item->id),
                        'force_delete_route' => route('variants.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($variants);
        }

        if ($type === 'all' || $type === 'newsletters') {
            $newsletters = Newsletter::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'newsletter',
                        'type_label' => 'Email đăng ký',
                        'name' => $item->email,
                        'description' => '',
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('admin.newsletters.restore', $item->id),
                        'force_delete_route' => route('admin.newsletters.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($newsletters);
        }

        if ($type === 'all' || $type === 'contacts') {
            $contacts = Contact::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'contact',
                        'type_label' => 'Liên hệ',
                        'name' => $item->name,
                        'description' => $item->subject,
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('admin.contacts.restore', $item->id),
                        'force_delete_route' => route('admin.contacts.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($contacts);
        }

        if ($type === 'all' || $type === 'warehouses') {
            $warehouses = Warehouse::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('warehouse_name', 'like', "%{$search}%");
                })
                ->with('manager')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'warehouse',
                        'type_label' => 'Kho hàng',
                        'name' => $item->warehouse_name,
                        'description' => $item->address ?? '',
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('inventories.warehouse.restore', $item->id),
                        'force_delete_route' => route('inventories.warehouse.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($warehouses);
        }

        if ($type === 'all' || $type === 'discounts') {
            $discounts = Discount::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'discount',
                        'type_label' => 'Mã giảm giá',
                        'name' => $item->code,
                        'description' => 'Giá trị: ' . ($item->discount_type === 'percent' ? $item->discount_value . '%' : number_format($item->discount_value) . ' VNĐ'),
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('admin.discounts.restore', $item->id),
                        'force_delete_route' => route('admin.discounts.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($discounts);
        }

        if ($type === 'all' || $type === 'posts') {
            $posts = Post::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })
                ->with('category')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'post',
                        'type_label' => 'Bài viết',
                        'name' => $item->title,
                        'description' => $item->category ? $item->category->category_name : '',
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('post.restore', $item->id),
                        'force_delete_route' => route('post.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($posts);
        }

        if ($type === 'all' || $type === 'banners') {
            $banners = Banner::onlyTrashed()
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'banner',
                        'type_label' => 'Banner',
                        'name' => 'Banner #' . $item->id,
                        'description' => '',
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('banner.restore', $item->id),
                        'force_delete_route' => route('banner.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($banners);
        }

        if ($type === 'all' || $type === 'customers') {
            $customers = Customer::onlyTrashed()
                ->when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'customer',
                        'type_label' => 'Khách hàng',
                        'name' => $item->name,
                        'description' => $item->email . ' - ' . $item->phone,
                        'deleted_at' => $item->deleted_at,
                        'restore_route' => route('admin.customers.restore', $item->id),
                        'force_delete_route' => route('admin.customers.force-delete', $item->id),
                    ];
                });
            $allItems = $allItems->merge($customers);
        }

        // Sắp xếp theo thời gian xóa (mới nhất trước)
        $allItems = $allItems->sortByDesc('deleted_at');

        // Phân trang thủ công
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $items = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $allItems->count();
        $lastPage = ceil($total / $perPage);

        // Đếm số lượng theo từng loại
        $counts = [
            'all' => Product::onlyTrashed()->count() +
                    Category::onlyTrashed()->count() +
                    Brand::onlyTrashed()->count() +
                    ProductVariant::onlyTrashed()->count() +
                    Newsletter::onlyTrashed()->count() +
                    Contact::onlyTrashed()->count() +
                    Warehouse::onlyTrashed()->count() +
                    Discount::onlyTrashed()->count() +
                    Post::onlyTrashed()->count() +
                    Banner::onlyTrashed()->count() +
                    Customer::onlyTrashed()->count(),
            'products' => Product::onlyTrashed()->count(),
            'categories' => Category::onlyTrashed()->count(),
            'brands' => Brand::onlyTrashed()->count(),
            'variants' => ProductVariant::onlyTrashed()->count(),
            'newsletters' => Newsletter::onlyTrashed()->count(),
            'contacts' => Contact::onlyTrashed()->count(),
            'warehouses' => Warehouse::onlyTrashed()->count(),
            'discounts' => Discount::onlyTrashed()->count(),
            'posts' => Post::onlyTrashed()->count(),
            'banners' => Banner::onlyTrashed()->count(),
            'customers' => Customer::onlyTrashed()->count(),
        ];

        return view('admin.trash.index', compact('items', 'type', 'search', 'counts', 'currentPage', 'lastPage', 'total', 'perPage'));
    }
}
