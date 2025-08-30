<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use App\Models\Promoter;
use App\Models\ReferralSource;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
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

        // get user with id 1 and send him a filament notification to database with message "welcome to tayssir" in arabic and 1 action button primary with go too google
        // $user = User::find(1);
        // if ($user) {
        //     Notification::make()
        //         ->title('دفعة جديدة')
        //         ->success()
        //         ->body('لديك دفعة جديدة من المستخدم (اسم المستخدم) باستخدام شارجيلي للاشتراك (اسم الاشتراك)')
        //         ->actions([
        //             Action::make('عرض الفاتورة')
        //                 ->url('#')
        //                 ->button()
        //                 ->color("primary")
        //                 // ->markAsRead()
        //         ])
        //         ->sendToDatabase($user);
        // }
    }
}
