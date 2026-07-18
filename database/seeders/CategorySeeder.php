<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Goldfish', 'name_bn' => 'গোল্ডফিশ', 'slug' => 'goldfish'],
            ['name_en' => 'Betta', 'name_bn' => 'বেটা', 'slug' => 'betta'],
            ['name_en' => 'Livebearers', 'name_bn' => 'লাইভবিয়ারার', 'slug' => 'livebearers'],
            ['name_en' => 'Tetra', 'name_bn' => 'টেট্রা', 'slug' => 'tetra'],
            ['name_en' => 'Barb', 'name_bn' => 'বার্ব', 'slug' => 'barb'],
            ['name_en' => 'Gourami', 'name_bn' => 'গৌরামি', 'slug' => 'gourami'],
            ['name_en' => 'Cichlid', 'name_bn' => 'সিচলিড', 'slug' => 'cichlid'],
            ['name_en' => 'Catfish/Bottom Dweller', 'name_bn' => 'ক্যাটফিশ/তলবাসী', 'slug' => 'catfish-bottom-dweller'],
            ['name_en' => 'Shark Type', 'name_bn' => 'শার্ক টাইপ', 'slug' => 'shark-type'],
            ['name_en' => 'Nano Fish', 'name_bn' => 'ন্যানো মাছ', 'slug' => 'nano-fish'],
            ['name_en' => 'Premium/Rare', 'name_bn' => 'প্রিমিয়াম/বিরল', 'slug' => 'premium-rare'],
            ['name_en' => 'Invertebrate', 'name_bn' => 'অমেরুদণ্ডী', 'slug' => 'invertebrate'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name_en' => $category['name_en'],
                    'name_bn' => $category['name_bn'],
                    'description_en' => 'Ornamental '.$category['name_en'].' for home aquariums.',
                    'description_bn' => 'ঘরোয়া অ্যাকোয়ারিয়ামের জন্য শোভাময় '.$category['name_bn'].'.',
                ]
            );
        }
    }
}
