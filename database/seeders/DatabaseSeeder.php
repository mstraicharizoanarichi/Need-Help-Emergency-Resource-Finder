<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ResourceCategorySeeder::class,
            UserSeeder::class,
            ResourceSeeder::class,
            DoctorSeeder::class,
            BloodDonorSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
