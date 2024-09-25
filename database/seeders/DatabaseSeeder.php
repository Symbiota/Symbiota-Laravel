<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     * Note Only Universal Portal Data should be here
     */
    public function run(): void {
        $this->call(
            TaxonomySeeder::class,
        );
    }
}
