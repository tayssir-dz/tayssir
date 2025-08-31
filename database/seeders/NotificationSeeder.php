<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where('email', 'admin@admin.dev')->first();
        if ($user) {
            $faker = Faker::create('ar_SA');
            for ($i = 0; $i < 20; $i++) {
                $user->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'App\Notifications\WelcomeNotification', // optional, can put anything
                    'data' => [
                        'title' => $faker->name(), // random Arabic title
                        'body'  => $faker->company() . " - " . $faker->city(), // random Arabic body
                    ],
                    'read_at' => null,
                ]);
            }
        }
    }
}
