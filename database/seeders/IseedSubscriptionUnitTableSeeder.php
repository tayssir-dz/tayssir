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
        
        \DB::table('subscription_unit')->insert(array (
            0 => 
            array (
                'id' => 33,
                'subscription_id' => 1,
                'unit_id' => 5,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 34,
                'subscription_id' => 1,
                'unit_id' => 6,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}