<?php

namespace Database\Factories;

use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomeRecord>
 */
class IncomeRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $playCount = fake()->numberBetween(10, 200);
        $pricePerPlay = fake()->randomElement([2_000, 5_000, 10_000]);

        return [
            'machine_id' => Machine::factory(),
            'user_id' => User::factory(),
            'date' => fake()->date(),
            'play_count' => $playCount,
            // Keep total_income consistent with play_count x price_per_play.
            'total_income' => $playCount * $pricePerPlay,
        ];
    }
}
