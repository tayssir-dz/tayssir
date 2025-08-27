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
            WilayaCommuneSeeder::class,
            GuestSubscriptionSeeder::class,
        ]);

        ReferralSource::factory()->createMany([
            ['name' => 'Facebook'],
            ['name' => 'Instagram'],
            ['name' => 'Twitter'],
            ['name' => 'Friend'],
            ['name' => 'Google Search'],
            ['name' => 'Other'],
        ]);
    }
}
