<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateProductPricesSeeder extends Seeder
{
    /**
     * Cập nhật giá tất cả sản phẩm về khoảng 100k-150k
     */
    public function run(): void
    {
        $products = Product::all();
        $count = 0;

        foreach ($products as $index => $product) {
            // Giá trong khoảng 100k-150k, mỗi sản phẩm tăng 5k
            $newPrice = 100000 + (($index % 10 + 1) * 5000); // 100k, 105k, 110k, ..., 145k
            
            // Giá sale (nếu có) giảm 10-20k so với giá gốc
            $newSalePrice = null;
            if ($index % 2 === 0) {
                $discount = 10000 + (($index % 3) * 5000); // 10k, 15k, hoặc 20k
                $newSalePrice = max(95000, $newPrice - $discount);
            }

            $product->update([
                'price' => $newPrice,
                'sale_price' => $newSalePrice,
            ]);

            $count++;
        }

        $this->command->info("Đã cập nhật giá cho {$count} sản phẩm về khoảng 100k-150k!");
    }
}

