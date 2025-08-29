<?php

namespace Database\Seeders;

use App\Models\ReferralSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReferralSource::insert([
            ['name' => 'Facebook'],
            ['name' => 'Instagram'],
            ['name' => 'Twitter'],
            ['name' => 'Friend'],
            ['name' => 'Google Search'],
            ['name' => 'Other'],
        ]);
    }
}
