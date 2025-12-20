<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Clothing and accessories',
            ],
            [
                'category_name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets',
            ],
            [
                'category_name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Shoes and boots',
            ],
            [
                'category_name' => 'Sportswear',
                'slug' => 'sportswear',
                'description' => 'Sports and athletic clothing',
            ],
            [
                'category_name' => 'Watches',
                'slug' => 'watches',
                'description' => 'Timepieces and accessories',
            ],
            [
                'category_name' => 'Furniture',
                'slug' => 'furniture',
                'description' => 'Home and office furniture',
            ],
            [
                'category_name' => 'Appliances',
                'slug' => 'appliances',
                'description' => 'Home and kitchen appliances',
            ],
            [
                'category_name' => 'Headphones',
                'slug' => 'headphones',
                'description' => 'Audio equipment and accessories',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
