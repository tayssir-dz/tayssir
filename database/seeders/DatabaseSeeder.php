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
            GuestSubscriptionSeeder::class
        ]);

        // User::factory(10)->create();

        // Create the super_admin role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.dev',
            'password' => bcrypt('admin'),
        ]);
        $user->assignRole($role);
    }
}
