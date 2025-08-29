<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use App\Models\Promoter;
use App\Models\ReferralSource;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // WilayaCommuneSeeder::class,
            // PromoterSeeder::class
            // GuestSubscriptionSeeder::class,
            // ReferralSourceSeeder::class
        ]);
    }
}
