<?php

namespace Database\Factories;

use App\Models\Promoter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromoCode>
 */
class PromoCodeFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');

        return [
            'promoter_id' => Promoter::factory(),
            'code' => strtoupper($this->faker->unique()->bothify('PROMO###??')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'student_discount' => $this->faker->randomFloat(2, 5, 50), // 5% to 50%
            'promoter_margin' => $this->faker->randomFloat(2, 2, 20), // 2% to 20%
        ];
    }

    /**
     * Create an active promo code (currently valid)
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::now()->subDays($this->faker->numberBetween(1, 30));
            $endDate = Carbon::now()->addDays($this->faker->numberBetween(1, 90));

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Create an expired promo code
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::now()->subDays($this->faker->numberBetween(60, 120));
            $endDate = Carbon::now()->subDays($this->faker->numberBetween(1, 30));

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Create a future promo code (not yet active)
     */
    public function future(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::now()->addDays($this->faker->numberBetween(1, 30));
            $endDate = Carbon::now()->addDays($this->faker->numberBetween(31, 120));

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Create a high discount promo code
     */
    public function highDiscount(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'student_discount' => $this->faker->randomFloat(2, 30, 70), // 30% to 70%
                'promoter_margin' => $this->faker->randomFloat(2, 10, 25), // 10% to 25%
            ];
        });
    }

    /**
     * Create a low discount promo code
     */
    public function lowDiscount(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'student_discount' => $this->faker->randomFloat(2, 2, 15), // 2% to 15%
                'promoter_margin' => $this->faker->randomFloat(2, 1, 5), // 1% to 5%
            ];
        });
    }
}
