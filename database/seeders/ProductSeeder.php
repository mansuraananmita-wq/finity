<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = require database_path('data/product_catalog.php');
        $categoryMap = Category::pluck('id', 'slug')->mapWithKeys(function ($id, $slug) {
            return [$slug => $id];
        });

        $slugCategoryMap = [
            'Goldfish' => 'goldfish',
            'Betta' => 'betta',
            'Livebearers' => 'livebearers',
            'Tetra' => 'tetra',
            'Barb' => 'barb',
            'Gourami' => 'gourami',
            'Cichlid' => 'cichlid',
            'Catfish/Bottom Dweller' => 'catfish-bottom-dweller',
            'Shark Type' => 'shark-type',
            'Nano Fish' => 'nano-fish',
            'Premium/Rare' => 'premium-rare',
            'Invertebrate' => 'invertebrate',
        ];

        foreach ($catalog as $row) {
            $categorySlug = $slugCategoryMap[$row['category']] ?? Str::slug($row['category']);
            $categoryId = Category::where('slug', $categorySlug)->value('id');

            if (! $categoryId) {
                continue;
            }

            $slug = Str::slug($row['name_en']);

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'name_en' => $row['name_en'],
                    'name_bn' => $row['name_bn'],
                    'scientific_name' => $row['scientific_name'],
                    'price' => $row['price'],
                    'stock_qty' => $row['stock_qty'],
                    'description_en' => $row['description_en'],
                    'description_bn' => $row['description_bn'],
                    'care_level' => $row['care_level'],
                    'tank_size_liters' => $row['tank_size_liters'],
                    'weight_grams' => $row['weight_grams'],
                    'image_path' => '/images/products/'.Str::slug($row['name_en']).'.jpg',
                    'is_featured' => $row['is_featured'] ?? false,
                ]
            );
        }
    }
}
