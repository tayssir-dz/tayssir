<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class GuestSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::firstOrCreate(
            ['name' => 'Guest'],
            [
                'description' => 'Default subscription for all users',
                'price' => 0,
                'ending_date' => null,
            ]
        );
    }
}
