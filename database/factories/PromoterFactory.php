<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Kossa\AlgerianCities\Wilaya;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promoter>
 */
class PromoterFactory extends Factory
{
    public function definition(): array
    {
        $wilaya = Wilaya::all()->random();
        $commune = $wilaya->communes->random();

        return [
            'name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'wilaya_id' => $wilaya->id,
            'commune_id' => $commune->id,
        ];
    }
}
