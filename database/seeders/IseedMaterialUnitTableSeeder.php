<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedMaterialUnitTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('material_unit')->delete();

        \DB::table('material_unit')->insert([
            0 => [
                'id' => 30,
                'material_id' => 3,
                'unit_id' => 5,
                'sort' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 31,
                'material_id' => 3,
                'unit_id' => 6,
                'sort' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

    }
}
