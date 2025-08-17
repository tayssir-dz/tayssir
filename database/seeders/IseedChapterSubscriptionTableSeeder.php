<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IseedChapterSubscriptionTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('chapter_subscription')->delete();

        \DB::table('chapter_subscription')->insert([
            0 => [
                'id' => 123,
                'subscription_id' => 1,
                'chapter_id' => 9,
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 124,
                'subscription_id' => 1,
                'chapter_id' => 10,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

    }
}
