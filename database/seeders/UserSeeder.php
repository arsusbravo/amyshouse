<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = require database_path('seeders/data/users.php');

        $this->command->info("Importing " . count($users) . " users...");

        $imported = 0;
        $skipped = 0;

        foreach ($users as $userData) {
            $existing = DB::table('users')->where('email', $userData['email'])->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            DB::table('users')->insert([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'], // preserved bcrypt hash
                'phone' => $userData['phone'],
                'group_id' => $userData['group_id'],
                'is_admin' => $userData['is_admin'],
                'is_active' => $userData['is_active'],
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $imported++;
        }

        $this->command->info("Done: {$imported} imported, {$skipped} skipped (duplicate email).");
    }
}
