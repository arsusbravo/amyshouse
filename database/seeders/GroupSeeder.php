<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'id' => 1,
                'name' => 'Zuid-Holland (Rotterdam, Den Haag, Delft)',
                'description' => null,
                'sort_order' => 0,
                'created_at' => '2020-09-18 23:41:27',
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Amstelveen',
                'description' => null,
                'sort_order' => 1,
                'created_at' => '2020-09-18 23:41:54',
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Eindhoven',
                'description' => null,
                'sort_order' => 2,
                'created_at' => '2021-03-29 06:50:47',
                'updated_at' => now(),
            ],
        ];

        foreach ($groups as $group) {
            DB::table('groups')->updateOrInsert(
                ['id' => $group['id']],
                $group,
            );
        }
    }
}
