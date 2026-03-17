<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['id' => 1, 'slug' => 'labubu-clothes', 'name_zh' => 'Labubu衣服', 'name_en' => 'Labubu Clothes', 'sort_order' => 0],
            ['id' => 2, 'slug' => 'real-clothes', 'name_zh' => '真人衣服', 'name_en' => 'Clothes', 'sort_order' => 1],
            ['id' => 3, 'slug' => 'bag', 'name_zh' => '包包', 'name_en' => 'Bags', 'sort_order' => 2],
            ['id' => 4, 'slug' => 'accessory', 'name_zh' => '配件', 'name_en' => 'Accessories', 'sort_order' => 3],
        ];

        foreach ($types as $type) {
            DB::table('product_types')->updateOrInsert(
                ['id' => $type['id']],
                [...$type, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
