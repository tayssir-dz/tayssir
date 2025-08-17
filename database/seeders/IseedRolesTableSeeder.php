<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedRolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('roles')->delete();

        \DB::table('roles')->insert([
            0 => [
                'id' => 1,
                'name' => 'super_admin',
                'guard_name' => 'web',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-13 01:16:37',
            ],
            1 => [
                'id' => 2,
                'name' => 'student',
                'guard_name' => 'web',
                'created_at' => '2025-03-18 02:08:00',
                'updated_at' => '2025-03-18 02:08:00',
            ],
        ]);

    }
}
