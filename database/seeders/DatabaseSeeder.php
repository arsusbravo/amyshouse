<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,       // groups first (users FK)
            UserSeeder::class,        // users depend on groups
            ProductTypeSeeder::class, // product_types (products FK)
            MaterialSeeder::class,    // materials (pivot FK)
            ColorSeeder::class,       // colors (pivot FK)
            ProductSeeder::class,     // products + translations + pivots
        ]);
    }
}
