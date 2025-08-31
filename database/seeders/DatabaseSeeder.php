<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use App\Models\Promoter;
use App\Models\ReferralSource;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Faker\Factory as Faker;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // WilayaCommuneSeeder::class,
            // PromoterSeeder::class
            // GuestSubscriptionSeeder::class,
            // ReferralSourceSeeder::class
        ]);

        $user = \App\Models\User::where('email', 'fbekkouche14@gmail.com')->first();
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
