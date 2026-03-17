<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['id' => 1, 'slug' => 'pink', 'name_zh' => '粉紅', 'name_en' => 'Pink', 'hex_code' => '#FFB6C1', 'sort_order' => 0],
            ['id' => 2, 'slug' => 'red', 'name_zh' => '紅色', 'name_en' => 'Red', 'hex_code' => '#DC3545', 'sort_order' => 1],
            ['id' => 3, 'slug' => 'white', 'name_zh' => '白色', 'name_en' => 'White', 'hex_code' => '#FFFFFF', 'sort_order' => 2],
            ['id' => 4, 'slug' => 'cream', 'name_zh' => '米白', 'name_en' => 'Cream', 'hex_code' => '#FFFDD0', 'sort_order' => 3],
            ['id' => 5, 'slug' => 'beige', 'name_zh' => '米色', 'name_en' => 'Beige', 'hex_code' => '#F5F5DC', 'sort_order' => 4],
            ['id' => 6, 'slug' => 'brown', 'name_zh' => '咖啡色', 'name_en' => 'Brown', 'hex_code' => '#8B4513', 'sort_order' => 5],
            ['id' => 7, 'slug' => 'green', 'name_zh' => '綠色', 'name_en' => 'Green', 'hex_code' => '#6DBE76', 'sort_order' => 6],
            ['id' => 8, 'slug' => 'mint', 'name_zh' => '薄荷綠', 'name_en' => 'Mint', 'hex_code' => '#AAF0D1', 'sort_order' => 7],
            ['id' => 9, 'slug' => 'blue', 'name_zh' => '藍色', 'name_en' => 'Blue', 'hex_code' => '#6495ED', 'sort_order' => 8],
            ['id' => 10, 'slug' => 'navy', 'name_zh' => '深藍', 'name_en' => 'Navy', 'hex_code' => '#1B2A4A', 'sort_order' => 9],
            ['id' => 11, 'slug' => 'purple', 'name_zh' => '紫色', 'name_en' => 'Purple', 'hex_code' => '#9B59B6', 'sort_order' => 10],
            ['id' => 12, 'slug' => 'lavender', 'name_zh' => '薰衣草紫', 'name_en' => 'Lavender', 'hex_code' => '#E6E6FA', 'sort_order' => 11],
            ['id' => 13, 'slug' => 'yellow', 'name_zh' => '黃色', 'name_en' => 'Yellow', 'hex_code' => '#FFD700', 'sort_order' => 12],
            ['id' => 14, 'slug' => 'orange', 'name_zh' => '橘色', 'name_en' => 'Orange', 'hex_code' => '#FF8C00', 'sort_order' => 13],
            ['id' => 15, 'slug' => 'black', 'name_zh' => '黑色', 'name_en' => 'Black', 'hex_code' => '#222222', 'sort_order' => 14],
            ['id' => 16, 'slug' => 'grey', 'name_zh' => '灰色', 'name_en' => 'Grey', 'hex_code' => '#A0A0A0', 'sort_order' => 15],
            ['id' => 17, 'slug' => 'multicolor', 'name_zh' => '多色', 'name_en' => 'Multicolor', 'hex_code' => null, 'sort_order' => 16],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->updateOrInsert(
                ['id' => $color['id']],
                [...$color, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
