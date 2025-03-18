<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedChapterQuestionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('chapter_question')->delete();
        
        \DB::table('chapter_question')->insert(array (
            0 => 
            array (
                'id' => 938,
                'chapter_id' => 9,
                'question_id' => 81,
                'sort' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 939,
                'chapter_id' => 9,
                'question_id' => 82,
                'sort' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 940,
                'chapter_id' => 10,
                'question_id' => 83,
                'sort' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}