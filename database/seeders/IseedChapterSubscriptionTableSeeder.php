<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedChapterSubscriptionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('chapter_subscription')->delete();
        
        \DB::table('chapter_subscription')->insert(array (
            0 => 
            array (
                'id' => 123,
                'subscription_id' => 1,
                'chapter_id' => 9,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 124,
                'subscription_id' => 1,
                'chapter_id' => 10,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}