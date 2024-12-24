<?php

namespace Database\Seeders;

use App\Models\Chapter;
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
        User::factory(10)->create();
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.dev',
            'password' => bcrypt('admin'),
        ]);
        $user->assignRole($role);

        $this->call([
            ShieldSeeder::class,
            WilayaCommuneSeeder::class,
            GuestSubscriptionSeeder::class,
            ContentSeeder::class
        ]);

        $chapters = Chapter::all();
        $chapters->map(function ($chapter) {
            $seeder = new ContentSeeder();
            $seeder->generateQuestionsForChapter($chapter);
            $chapter->distributeDifficulties();
        });
    }
}
