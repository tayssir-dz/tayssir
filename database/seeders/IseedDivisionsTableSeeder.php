<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedDivisionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('divisions')->delete();
        
        \DB::table('divisions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'رياضيات',
                'image' => NULL,
                'description' => 'شعبة الرياضيات للبكالوريا الجزائرية',
                'created_at' => '2025-03-18 02:02:20',
                'updated_at' => '2025-03-18 02:02:20',
            ),
        ));
        
        
    }
}