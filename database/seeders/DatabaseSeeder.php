<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\PromoCode;
use App\Models\Promoter;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // $this->call(WilayaCommuneSeeder::class);
        // $this->call(IseedSeeder::class);

        Promoter::truncate();
        Promoter::factory(10)
            ->has(PromoCode::factory(2), 'promoCodes')
            ->create();
    }
}
