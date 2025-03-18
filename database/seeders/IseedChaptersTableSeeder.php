<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedChaptersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('chapters')->delete();
        
        \DB::table('chapters')->insert(array (
            0 => 
            array (
                'id' => 9,
                'name' => 'قوانين الاحتمالات',
                'description' => 'دراسة القوانين الأساسية للاحتمالات',
                'created_at' => '2025-03-18 02:17:35',
                'updated_at' => '2025-03-18 02:17:35',
            ),
            1 => 
            array (
                'id' => 10,
                'name' => 'النهايات والاتصال',
                'description' => 'دراسة نهايات الدوال واتصالها',
                'created_at' => '2025-03-18 02:23:47',
                'updated_at' => '2025-03-18 02:23:47',
            ),
        ));
        
        
    }
}