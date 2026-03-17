<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['id' => 1, 'slug' => 'wool', 'name_zh' => '羊毛', 'name_en' => 'Wool', 'sort_order' => 0],
            ['id' => 2, 'slug' => 'satin', 'name_zh' => '緞面', 'name_en' => 'Satin', 'sort_order' => 1],
            ['id' => 3, 'slug' => 'cotton', 'name_zh' => '棉', 'name_en' => 'Cotton', 'sort_order' => 2],
            ['id' => 4, 'slug' => 'cotton-yarn', 'name_zh' => '棉線', 'name_en' => 'Cotton Yarn', 'sort_order' => 3],
            ['id' => 5, 'slug' => 'acrylic-yarn', 'name_zh' => '壓克力線', 'name_en' => 'Acrylic Yarn', 'sort_order' => 4],
            ['id' => 6, 'slug' => 'linen', 'name_zh' => '亞麻', 'name_en' => 'Linen', 'sort_order' => 5],
            ['id' => 7, 'slug' => 'fleece', 'name_zh' => '搖粒絨', 'name_en' => 'Fleece', 'sort_order' => 6],
        ];

        foreach ($materials as $material) {
            DB::table('materials')->updateOrInsert(
                ['id' => $material['id']],
                [...$material, 'created_at' => now(), 'updated_at' => now()],
            );
        }
    }
}
