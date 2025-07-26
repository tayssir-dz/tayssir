<?php

namespace Database\Seeders;

use App\Models\Chapter;
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

        Promoter::factory(10)->create();
    }
}
