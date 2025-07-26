<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IseedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed roles and permissions first
        $this->call(IseedRolesTableSeeder::class);
        $this->call(IseedPermissionsTableSeeder::class);
        $this->call(IseedRoleHasPermissionsTableSeeder::class);

        // 2. Seed base entities in proper order
        $this->call(IseedDivisionsTableSeeder::class);  // Users depend on divisions
        $this->call(IseedUsersTableSeeder::class);      // Moved after divisions
        $this->call(IseedMaterialsTableSeeder::class);
        $this->call(IseedUnitsTableSeeder::class);
        $this->call(IseedChaptersTableSeeder::class);
        $this->call(IseedQuestionsTableSeeder::class);
        $this->call(IseedDiscountsTableSeeder::class);
        $this->call(IseedSubscriptionsTableSeeder::class);

        // 3. Seed relationship tables
        $this->call(IseedModelHasRolesTableSeeder::class);
        $this->call(IseedModelHasPermissionsTableSeeder::class);
        $this->call(IseedDivisionMaterialTableSeeder::class);
        $this->call(IseedMaterialUnitTableSeeder::class);
        $this->call(IseedChapterUnitTableSeeder::class);
        $this->call(IseedSubscriptionUnitTableSeeder::class);
        $this->call(IseedChapterSubscriptionTableSeeder::class);
        $this->call(IseedDiscountSubscriptionTableSeeder::class);

        // 4. Seed tables with multiple dependencies
        $this->call(IseedChapterQuestionTableSeeder::class);
        $this->call(IseedUserAnswersTableSeeder::class);
        $this->call(IseedSubscriptionCardsTableSeeder::class);
    }
}
