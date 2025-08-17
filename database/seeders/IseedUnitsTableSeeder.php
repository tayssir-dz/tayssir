<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedUnitsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('units')->delete();

        \DB::table('units')->insert([
            0 => [
                'id' => 5,
                'name' => 'الاحتمالات والإحصاء',
                'description' => 'دراسة الاحتمالات والإحصاء للسنة الثالثة ثانوي',
                'created_at' => '2025-03-18 02:16:47',
                'updated_at' => '2025-03-18 02:16:47',
            ],
            1 => [
                'id' => 6,
                'name' => 'قوانين الاحتمالات',
                'description' => 'دراسة القوانين الأساسية للاحتمالات',
                'created_at' => '2025-03-18 02:22:45',
                'updated_at' => '2025-03-18 02:22:45',
            ],
        ]);

    }
}
