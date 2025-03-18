<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedChapterUnitTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('chapter_unit')->delete();
        
        \DB::table('chapter_unit')->insert(array (
            0 => 
            array (
                'id' => 122,
                'chapter_id' => 9,
                'unit_id' => 5,
                'sort' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 123,
                'chapter_id' => 10,
                'unit_id' => 6,
                'sort' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}