<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedSubscriptionUnitTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('subscription_unit')->delete();

        \DB::table('subscription_unit')->insert([
            0 => [
                'id' => 33,
                'subscription_id' => 1,
                'unit_id' => 5,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 34,
                'subscription_id' => 1,
                'unit_id' => 6,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

    }
}
