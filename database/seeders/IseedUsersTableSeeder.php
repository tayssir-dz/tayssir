<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'admin',
                'phone_number' => NULL,
                'email' => 'admin@admin.dev',
                'email_verified_at' => '2025-03-13 01:16:37',
                'password' => '$2y$12$EuOKnUiOQDxEHVh7Ln1So.s0DJuoRMPflxTrn2JDp1cI0PYqSKdIe',
                'division_id' => NULL,
                'remember_token' => '8gJMljpTeJ',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-17 03:32:25',
                'avatar_url' => NULL,
                'wilaya_id' => NULL,
                'commune_id' => NULL,
                'age' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'keziz mouayed',
                'phone_number' => '0552525052',
                'email' => 'm_keziz@estin.dz',
                'email_verified_at' => '2025-03-18 02:10:45',
                'password' => '$2y$12$wF9aXz5ZH0WsZzrtAsoSxe1Y9TNzOsldhp3nLglH6p1a1oTpdTZTW',
                'division_id' => 1,
                'remember_token' => NULL,
                'created_at' => '2025-03-18 02:08:00',
                'updated_at' => '2025-03-18 02:10:45',
                'avatar_url' => NULL,
                'wilaya_id' => 40,
                'commune_id' => 1323,
                'age' => NULL,
            ),
        ));
    }
}
