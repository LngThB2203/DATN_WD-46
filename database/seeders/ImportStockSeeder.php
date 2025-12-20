<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Database\Seeder;

class ImportStockSeeder extends Seeder
{
    /**
     * Nhập kho mẫu cho các sản phẩm và biến thể
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        
        if ($warehouses->isEmpty()) {
            $this->command->error('Không có kho nào! Vui lòng tạo kho trước.');
            return;
        }

        $warehouse = $warehouses->first(); // Lấy kho đầu tiên
        $this->command->info("Sử dụng kho: {$warehouse->warehouse_name}");

        $products = Product::with('variants')->get();

        foreach ($products as $product) {
            $this->command->info("Đang xử lý: {$product->name}");

            // Kiểm tra xem sản phẩm đã có trong kho chưa
            $hasStock = WarehouseProduct::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->whereNull('variant_id')
                ->exists();

            if (!$hasStock) {
                // Nhập kho cho sản phẩm chính (không có biến thể)
                WarehouseProduct::create([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'quantity' => rand(50, 200), // Số lượng ngẫu nhiên từ 50-200
                ]);
                $this->command->info("  ✓ Đã nhập kho sản phẩm chính");
            }

            // Nhập kho cho các biến thể
            foreach ($product->variants as $variant) {
                $hasVariantStock = WarehouseProduct::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouse->id)
                    ->where('variant_id', $variant->id)
                    ->exists();

                if (!$hasVariantStock) {
                    WarehouseProduct::create([
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'quantity' => rand(20, 100), // Số lượng ngẫu nhiên từ 20-100
                    ]);
                    $this->command->info("  ✓ Đã nhập kho biến thể: {$variant->sku}");
                }
            }
        }

        $totalStock = WarehouseProduct::sum('quantity');
        $totalItems = WarehouseProduct::count();
        
        $this->command->info("Hoàn thành! Đã nhập kho:");
        $this->command->info("  - Tổng số bản ghi: {$totalItems}");
        $this->command->info("  - Tổng số lượng: {$totalStock}");
    }
}

