<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Review;

class ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            'Sản phẩm tốt, đúng mô tả.',
            'Chất lượng ổn so với giá.',
            'Giao hàng nhanh, đóng gói cẩn thận.',
            'Tôi hài lòng với sản phẩm này.',
            'Sẽ ủng hộ shop lần sau.',
        ];

        $products = Product::query()->take(20)->get();

        foreach ($products as $product) {
            $count = rand(2, 4);
            for ($i = 0; $i < $count; $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => null,
                    'rating' => rand(3, 5),
                    'comment' => $comments[array_rand($comments)],
                ]);
            }
        }
    }
}
