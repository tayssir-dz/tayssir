<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedDivisionMaterialTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('division_material')->delete();

        \DB::table('division_material')->insert([
            0 => [
                'id' => 12,
                'division_id' => 1,
                'material_id' => 3,
                'sort' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

    }
}
