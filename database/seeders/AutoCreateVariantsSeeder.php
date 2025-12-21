<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use App\Models\VariantScent;
use App\Models\VariantConcentration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AutoCreateVariantsSeeder extends Seeder
{
    /**
     * Tự động tạo biến thể cho các sản phẩm chưa có biến thể hoặc thêm biến thể cho tất cả sản phẩm
     */
    public function run(): void
    {
        // Lấy tất cả các options có sẵn
        $sizes = VariantSize::all();
        $scents = VariantScent::all();
        $concentrations = VariantConcentration::all();

        // Kiểm tra xem có options nào không
        if ($sizes->isEmpty() && $scents->isEmpty() && $concentrations->isEmpty()) {
            $this->command->warn('Không có sizes, scents hoặc concentrations nào trong database.');
            $this->command->info('Đang tạo các options mặc định...');
            
            // Tạo sizes mặc định
            $size30 = VariantSize::firstOrCreate(['size_name' => '30ml']);
            $size50 = VariantSize::firstOrCreate(['size_name' => '50ml']);
            $size100 = VariantSize::firstOrCreate(['size_name' => '100ml']);
            $sizes = collect([$size30, $size50, $size100]);
            
            // Tạo scents mặc định
            $scentWoody = VariantScent::firstOrCreate(['scent_name' => 'Woody']);
            $scentCitrus = VariantScent::firstOrCreate(['scent_name' => 'Citrus']);
            $scentFloral = VariantScent::firstOrCreate(['scent_name' => 'Floral']);
            $scents = collect([$scentWoody, $scentCitrus, $scentFloral]);
            
            // Tạo concentrations mặc định
            $concEDT = VariantConcentration::firstOrCreate(['concentration_name' => 'Eau de Toilette (EDT)']);
            $concEDP = VariantConcentration::firstOrCreate(['concentration_name' => 'Eau de Parfum (EDP)']);
            $concentrations = collect([$concEDT, $concEDP]);
        }

        // Lấy các sản phẩm chưa có biến thể hoặc chỉ có 1 biến thể (để tạo thêm)
        $productsWithoutVariants = Product::doesntHave('variants')->get();
        
        // Lấy sản phẩm có đúng 1 biến thể
        $productsWithOneVariant = Product::has('variants')
            ->withCount('variants')
            ->get()
            ->filter(function($product) {
                return $product->variants_count === 1;
            });

        // Nếu không có sản phẩm nào cần xử lý, tạo biến thể cho tất cả sản phẩm
        if ($productsWithoutVariants->isEmpty() && $productsWithOneVariant->isEmpty()) {
            $this->command->info('Tất cả sản phẩm đã có biến thể.');
            $this->command->info('Đang tạo thêm biến thể cho tất cả sản phẩm...');
            
            // Lấy tất cả sản phẩm
            $allProducts = Product::all();
            $createdCount = 0;
            
            foreach ($allProducts as $product) {
                $product->load('variants');
                
                // Tạo thêm 2-3 biến thể mới với các combination khác nhau
                $existingVariants = $product->variants;
                
                // Lấy các size, scent, concentration chưa được sử dụng
                $usedSizeIds = $existingVariants->pluck('size_id')->filter()->unique();
                $usedScentIds = $existingVariants->pluck('scent_id')->filter()->unique();
                $usedConcIds = $existingVariants->pluck('concentration_id')->filter()->unique();
                
                // Chọn size, scent, concentration chưa dùng hoặc ngẫu nhiên
                $newSize = $sizes->whereNotIn('id', $usedSizeIds)->first() 
                    ?? $sizes->whereIn('id', $usedSizeIds)->random() 
                    ?? $sizes->random();
                    
                $newScent = $scents->whereNotIn('id', $usedScentIds)->first() 
                    ?? $scents->whereIn('id', $usedScentIds)->random() 
                    ?? $scents->random();
                    
                $newConc = $concentrations->whereNotIn('id', $usedConcIds)->first() 
                    ?? $concentrations->whereIn('id', $usedConcIds)->random() 
                    ?? $concentrations->random();
                
                // Tạo 2 biến thể mới
                for ($i = 1; $i <= 2; $i++) {
                    $sizeId = $newSize ? $newSize->id : null;
                    $scentId = $newScent ? $newScent->id : null;
                    $concId = $newConc ? $newConc->id : null;
                    
                    // Thử các combination khác nhau
                    if ($i === 2 && $sizes->count() > 1) {
                        $altSize = $sizes->where('id', '!=', $sizeId)->first();
                        $sizeId = $altSize ? $altSize->id : $sizeId;
                    }
                    
                    // Kiểm tra xem combination này đã tồn tại chưa
                    $exists = ProductVariant::where('product_id', $product->id)
                        ->where('size_id', $sizeId)
                        ->where('scent_id', $scentId)
                        ->where('concentration_id', $concId)
                        ->exists();
                    
                    if (!$exists) {
                        $baseSku = $product->sku ?? 'PROD-' . $product->id;
                        $variantCount = $product->variants->count() + $createdCount + 1;
                        $variantSku = $baseSku . '-VAR-' . $variantCount;
                        
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'scent_id' => $scentId,
                            'concentration_id' => $concId,
                            'sku' => $variantSku,
                            'price_adjustment' => null,
                            'gender' => 'unisex',
                        ]);
                        
                        $createdCount++;
                    }
                }
            }
            
            $this->command->info("Đã tạo thêm {$createdCount} biến thể mới cho tất cả sản phẩm.");
            return;
        }

        $totalProducts = $productsWithoutVariants->count() + $productsWithOneVariant->count();
        $this->command->info("Tìm thấy {$productsWithoutVariants->count()} sản phẩm chưa có biến thể.");
        $this->command->info("Tìm thấy {$productsWithOneVariant->count()} sản phẩm chỉ có 1 biến thể (sẽ tạo thêm).");
        $this->command->info("Tổng cộng: {$totalProducts} sản phẩm cần xử lý.");

        $createdCount = 0;

        // Xử lý sản phẩm chưa có biến thể
        foreach ($productsWithoutVariants as $product) {
            // Tạo ít nhất 1 biến thể mặc định cho mỗi sản phẩm
            // Sử dụng các options đầu tiên nếu có, nếu không thì để null
            
            $sizeId = $sizes->isNotEmpty() ? $sizes->first()->id : null;
            $scentId = $scents->isNotEmpty() ? $scents->first()->id : null;
            $concentrationId = $concentrations->isNotEmpty() ? $concentrations->first()->id : null;

            // Tạo SKU cho biến thể
            $baseSku = $product->sku ?? 'PROD-' . $product->id;
            $variantSku = $baseSku . '-VAR-1';

            // Kiểm tra xem biến thể đã tồn tại chưa
            $existingVariant = ProductVariant::where('product_id', $product->id)
                ->where('size_id', $sizeId)
                ->where('scent_id', $scentId)
                ->where('concentration_id', $concentrationId)
                ->first();

            if ($existingVariant) {
                continue;
            }

            ProductVariant::create([
                'product_id' => $product->id,
                'size_id' => $sizeId,
                'scent_id' => $scentId,
                'concentration_id' => $concentrationId,
                'sku' => $variantSku,
                'price_adjustment' => null, // Giá mặc định sẽ dùng giá sản phẩm
                'gender' => 'unisex',
            ]);

            $createdCount++;

            // Tùy chọn: Tạo thêm 2-3 biến thể nữa để có nhiều lựa chọn
            if ($sizes->count() > 1 && $createdCount % 3 !== 0) {
                $secondSize = $sizes->skip(1)->first();
                if ($secondSize) {
                    $secondSku = $baseSku . '-VAR-2';
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size_id' => $secondSize->id,
                        'scent_id' => $scentId,
                        'concentration_id' => $concentrationId,
                        'sku' => $secondSku,
                        'price_adjustment' => null,
                        'gender' => 'unisex',
                    ]);
                    $createdCount++;
                }
            }

            if ($scents->count() > 1 && $createdCount % 3 === 0) {
                $secondScent = $scents->skip(1)->first();
                if ($secondScent) {
                    $thirdSku = $baseSku . '-VAR-3';
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size_id' => $sizeId,
                        'scent_id' => $secondScent->id,
                        'concentration_id' => $concentrationId,
                        'sku' => $thirdSku,
                        'price_adjustment' => null,
                        'gender' => 'unisex',
                    ]);
                    $createdCount++;
                }
            }
        }

        // Xử lý sản phẩm chỉ có 1 biến thể (tạo thêm biến thể)
        foreach ($productsWithOneVariant as $product) {
            // Load lại variants để đảm bảo có dữ liệu
            $product->load('variants');
            // Lấy biến thể hiện tại
            $currentVariant = $product->variants->first();
            
            // Tạo biến thể mới với size/scent/concentration khác
            $sizeId = null;
            $scentId = null;
            $concentrationId = null;

            // Thử tìm size khác
            if ($sizes->count() > 1) {
                $otherSize = $sizes->where('id', '!=', $currentVariant->size_id)->first();
                $sizeId = $otherSize ? $otherSize->id : ($sizes->first()->id ?? null);
            } else {
                $sizeId = $currentVariant->size_id;
            }

            // Thử tìm scent khác
            if ($scents->count() > 1) {
                $otherScent = $scents->where('id', '!=', $currentVariant->scent_id)->first();
                $scentId = $otherScent ? $otherScent->id : ($scents->first()->id ?? null);
            } else {
                $scentId = $currentVariant->scent_id;
            }

            // Thử tìm concentration khác
            if ($concentrations->count() > 1) {
                $otherConc = $concentrations->where('id', '!=', $currentVariant->concentration_id)->first();
                $concentrationId = $otherConc ? $otherConc->id : ($concentrations->first()->id ?? null);
            } else {
                $concentrationId = $currentVariant->concentration_id;
            }

            // Kiểm tra xem biến thể này đã tồn tại chưa
            $existingVariant = ProductVariant::where('product_id', $product->id)
                ->where('size_id', $sizeId)
                ->where('scent_id', $scentId)
                ->where('concentration_id', $concentrationId)
                ->first();

            if (!$existingVariant) {
                $baseSku = $product->sku ?? 'PROD-' . $product->id;
                $variantCount = $product->variants->count() + 1;
                $variantSku = $baseSku . '-VAR-' . $variantCount;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'size_id' => $sizeId,
                    'scent_id' => $scentId,
                    'concentration_id' => $concentrationId,
                    'sku' => $variantSku,
                    'price_adjustment' => null,
                    'gender' => 'unisex',
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Đã tạo {$createdCount} biến thể mới cho các sản phẩm.");
    }
}
