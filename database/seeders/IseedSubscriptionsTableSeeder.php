<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedSubscriptionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('subscriptions')->delete();

        \DB::table('subscriptions')->insert([
            0 => [
                'id' => 1,
                'name' => 'ضيف',
                'description' => 'اشتراك ضيف, لا يحتوي على اي مزايا',
                'gradiant_start' => '#f5f5f5',
                'gradiant_end' => '#f5f5f5',
                'bottom_color_at_start' => 0,
                'price' => 0,
                'ending_date' => null,
                'created_at' => '2025-03-18 02:02:20',
                'updated_at' => '2025-03-18 02:02:20',
            ],
        ]);
    }
}
