<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use App\Models\VariantScent;
use App\Models\VariantConcentration;
use App\Models\WarehouseProduct;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo biến thể (Size, Scent, Concentration)
        $sizes = [
            ['name' => '50ml', 'code' => 'S50'],
            ['name' => '100ml', 'code' => 'S100'],
            ['name' => '200ml', 'code' => 'S200'],
        ];
        foreach ($sizes as $size) {
            VariantSize::firstOrCreate(['name' => $size['name']], $size);
        }

        $scents = [
            ['name' => 'Hoa Hồng', 'code' => 'ROSE'],
            ['name' => 'Cam Chanh', 'code' => 'CITRUS'],
            ['name' => 'Gỗ Đàn Hương', 'code' => 'SANDALWOOD'],
            ['name' => 'Vanilla', 'code' => 'VANILLA'],
        ];
        foreach ($scents as $scent) {
            VariantScent::firstOrCreate(['name' => $scent['name']], $scent);
        }

        $concentrations = [
            ['name' => 'Eau de Toilette (EDT)', 'percentage' => 5, 'code' => 'EDT'],
            ['name' => 'Eau de Parfum (EDP)', 'percentage' => 15, 'code' => 'EDP'],
        ];
        foreach ($concentrations as $conc) {
            VariantConcentration::firstOrCreate(['name' => $conc['name']], $conc);
        }

        // 2. Tạo sản phẩm mẫu
        $products = [
            [
                'name' => 'Nước Hoa Hoa Hồng Classic',
                'slug' => 'nuoc-hoa-hoa-hong-classic',
                'sku' => 'PF-001',
                'price' => 450000,
                'description' => 'Nước hoa cao cấp với hương thơm thanh thoát từ hoa hồng tự nhiên',
            ],
            [
                'name' => 'Nước Hoa Cam Chanh Tươi',
                'slug' => 'nuoc-hoa-cam-chanh-tuoi',
                'sku' => 'PF-002',
                'price' => 500000,
                'description' => 'Hương cam chanh tươi mát, hoàn hảo cho mùa hè',
            ],
            [
                'name' => 'Nước Hoa Gỗ Đàn Hương',
                'slug' => 'nuoc-hoa-go-dan-huong',
                'sku' => 'PF-003',
                'price' => 550000,
                'description' => 'Mùi gỗ sang trọng, quyến rũ và bền lâu',
            ],
        ];

        $categoryId = 1; // Giả sử category ID là 1

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                [
                    'name' => $productData['name'],
                    'sku' => $productData['sku'],
                    'price' => $productData['price'],
                    'description' => $productData['description'],
                    'category_id' => $categoryId,
                    'status' => true,
                ]
            );

            // Tạo variants cho mỗi sản phẩm
            $sizes = VariantSize::all();
            $scents = VariantScent::all();
            $concentrations = VariantConcentration::all();

            foreach ($sizes as $size) {
                foreach ($scents as $scent) {
                    foreach ($concentrations as $concentration) {
                        // Tính giá dựa trên size
                        $priceMultiplier = match($size->name) {
                            '50ml' => 1,
                            '100ml' => 1.4,
                            '200ml' => 2.2,
                            default => 1,
                        };

                        $variantPrice = (int)($product->price * $priceMultiplier);

                        $variant = ProductVariant::firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'size_id' => $size->id,
                                'scent_id' => $scent->id,
                                'concentration_id' => $concentration->id,
                            ],
                            [
                                'sku' => $product->sku . '-' . $size->code . '-' . $scent->code . '-' . $concentration->code,
                                'price' => $variantPrice,
                            ]
                        );

                        // Thêm stock vào warehouse
                        WarehouseProduct::firstOrCreate(
                            [
                                'warehouse_id' => 1,
                                'product_variant_id' => $variant->id,
                            ],
                            [
                                'quantity' => rand(10, 50),
                            ]
                        );
                    }
                }
            }
        }

        // 3. Tạo đơn hàng mẫu
        $user = User::where('role', 'user')->first();
        if ($user) {
            for ($i = 1; $i <= 3; $i++) {
                $order = Order::firstOrCreate(
                    ['order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                    [
                        'user_id' => $user->id,
                        'customer_name' => $user->name,
                        'customer_email' => $user->email,
                        'customer_phone' => '0901234567',
                        'shipping_address_line' => '123 Đường Lê Lợi, TP. HCM',
                        'total_amount' => rand(500000, 2000000),
                        'status' => ['pending', 'processing', 'shipped', 'delivered'][rand(0, 3)],
                    ]
                );

                // Thêm detail cho đơn hàng
                $product = Product::inRandomOrder()->first();
                if ($product) {
                    $variant = $product->productVariants()->inRandomOrder()->first();
                    if ($variant) {
                        OrderDetail::firstOrCreate(
                            [
                                'order_id' => $order->id,
                                'product_variant_id' => $variant->id,
                            ],
                            [
                                'quantity' => rand(1, 3),
                                'price' => $variant->price,
                            ]
                        );
                    }
                }
            }
        }

        // 4. Tạo đánh giá mẫu
        $products = Product::all();
        foreach ($products as $product) {
            for ($i = 0; $i < 3; $i++) {
                Review::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'user_id' => $user->id ?? 2,
                    ],
                    [
                        'rating' => rand(3, 5),
                        'comment' => 'Sản phẩm ' . $product->name . ' rất tốt, mùi thơm lâu lâu. Giao hàng nhanh, dịch vụ chuyên nghiệp!',
                        'status' => 'approved',
                    ]
                );
            }
        }
    }
}
