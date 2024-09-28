<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Occurrence;
use Illuminate\Database\Seeder;

class OccurrenceSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Occurrence::factory()->create();
    }
}
