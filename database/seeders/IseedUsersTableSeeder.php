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

        \DB::table('users')->insert([
            0 => [
                'id' => 1,
                'name' => 'admin',
                'phone_number' => null,
                'email' => 'admin@admin.dev',
                'email_verified_at' => '2025-03-13 01:16:37',
                'password' => '$2y$12$EuOKnUiOQDxEHVh7Ln1So.s0DJuoRMPflxTrn2JDp1cI0PYqSKdIe',
                'division_id' => null,
                'remember_token' => '8gJMljpTeJ',
                'created_at' => '2025-03-13 01:16:37',
                'updated_at' => '2025-03-17 03:32:25',
                'avatar_url' => null,
                'wilaya_id' => null,
                'commune_id' => null,
                'age' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'keziz mouayed',
                'phone_number' => '0552525052',
                'email' => 'm_keziz@estin.dz',
                'email_verified_at' => '2025-03-18 02:10:45',
                'password' => '$2y$12$wF9aXz5ZH0WsZzrtAsoSxe1Y9TNzOsldhp3nLglH6p1a1oTpdTZTW',
                'division_id' => 1,
                'remember_token' => null,
                'created_at' => '2025-03-18 02:08:00',
                'updated_at' => '2025-03-18 02:10:45',
                'avatar_url' => null,
                'wilaya_id' => 40,
                'commune_id' => 1323,
                'age' => null,
            ],
        ]);
    }
}
