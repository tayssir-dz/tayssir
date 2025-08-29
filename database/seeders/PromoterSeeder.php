<?php

namespace Database\Seeders;

use App\Models\Promoter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoterSeeder extends Seeder
{
    public function run(): void
    {
        Promoter::factory()->count(10)->create();
    }
}
