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

        \DB::table('chapter_question')->insert([
            0 => [
                'id' => 938,
                'chapter_id' => 9,
                'question_id' => 81,
                'sort' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 939,
                'chapter_id' => 9,
                'question_id' => 82,
                'sort' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 940,
                'chapter_id' => 10,
                'question_id' => 83,
                'sort' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

    }
}
