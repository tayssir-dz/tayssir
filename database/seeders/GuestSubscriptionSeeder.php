<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class GuestSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::firstOrCreate(
            ['name' => 'ضيف'],
            [
                'description' => 'اشتراك ضيف, لا يحتوي على اي مزايا',
                'price' => 0,
                'ending_date' => null,
            ]
        );
    }
}
