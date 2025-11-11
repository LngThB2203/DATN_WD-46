<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductGallerySeeder extends Seeder
{
    public function run(): void
    {

        if (!Storage::disk('public')->exists('product-demo')) {
            Storage::disk('public')->makeDirectory('product-demo');
        }

        for ($i = 1; $i <= 8; $i++) {
            $slug = 'nuoc-hoa-' . $i;
            $product = Product::where('slug', $slug)->first();
            if (!$product) {
                continue; 
            }

            $destDir = 'product-demo/' . $slug;
            Storage::disk('public')->makeDirectory($destDir);

            $sourceImages = [
                public_path('assets/client/img/product/product-1.webp'),
                public_path('assets/client/img/product/product-2.webp'),
                public_path('assets/client/img/product/product-3.webp'),
            ];

            foreach ($sourceImages as $index => $sourcePath) {
                if (!File::exists($sourcePath)) {
                    continue;
                }

                $filename = 'image-' . ($index + 1) . '.webp';
                $storagePath = $destDir . '/' . $filename; 

                
                if (!Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->put($storagePath, File::get($sourcePath));
                }

                ProductGallery::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'image_path' => $storagePath,
                    ],
                    [
                        'alt_text' => 'Ảnh ' . ($index + 1) . ' - ' . $product->name,
                        'is_primary' => $index === 0,
                    ]
                );
            }
        }
    }
}
