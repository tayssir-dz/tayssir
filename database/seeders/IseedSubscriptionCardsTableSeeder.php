<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedSubscriptionCardsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('subscription_cards')->delete();

    }
}
