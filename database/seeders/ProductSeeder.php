<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = Category::value('id');

        for ($i = 1; $i <= 8; $i++) {
            Product::firstOrCreate(
                ['slug' => 'nuoc-hoa-' . $i],
                [
                    'name' => 'Nước hoa ' . $i,
                    'sku' => 'PF-DEMO-' . str_pad((string)$i, 3, '0', STR_PAD_LEFT),
                    'image' => null, // để view fallback ảnh tĩnh assets
                    'price' => 100000 + ($i * 5000), // giá VND 100k-140k
                    'sale_price' => $i % 2 === 0 ? (95000 + ($i * 4000)) : null,
                    'description' => 'Sản phẩm demo số ' . $i . ' dùng để kiểm tra trang chi tiết.',
                    'category_id' => $categoryId,
                    'status' => true,
                ]
            );
        }
    }
}
