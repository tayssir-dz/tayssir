<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            ShieldSeeder::class,
            WilayaCommuneSeeder::class,
        ]);

        // User::factory(10)->create();

        // $user = User::factory()->create([
        //     'name' => 'admin',
        //     'email' => 'admin@admin.dev',
        //     'password' => bcrypt('admin'),
        // ]);
        // $user->assignRole(Role::create(['name' => 'super_admin']));
    }
}
