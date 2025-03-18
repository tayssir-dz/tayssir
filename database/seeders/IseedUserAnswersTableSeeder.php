<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedUserAnswersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_answers')->delete();
        
        \DB::table('user_answers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 2,
                'question_id' => 81,
                'chapter_id' => 9,
                'unit_id' => 5,
                'material_id' => 3,
                'points_earned' => 1,
                'created_at' => '2025-03-18 02:20:51',
                'updated_at' => '2025-03-18 02:20:51',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 2,
                'question_id' => 82,
                'chapter_id' => 9,
                'unit_id' => 5,
                'material_id' => 3,
                'points_earned' => 0,
                'created_at' => '2025-03-18 02:20:51',
                'updated_at' => '2025-03-18 02:20:51',
            ),
        ));
        
        
    }
}