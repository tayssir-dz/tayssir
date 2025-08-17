<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedMaterialsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('materials')->delete();

        \DB::table('materials')->insert([
            0 => [
                'id' => 3,
                'name' => 'الرياضيات',
                'code' => 'MATH',
                'color' => '#3366FF',
                'secondary_color' => '#99BBFF',
                'description' => 'مادة الرياضيات للبكالوريا',
                'created_at' => '2025-03-18 02:16:20',
                'updated_at' => '2025-03-18 02:16:20',
            ],
        ]);

    }
}
