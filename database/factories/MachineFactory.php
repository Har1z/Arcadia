<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => 'Mesin '.fake()->unique()->word(),
            'serial_number' => 'ARC-'.fake()->unique()->numerify('####'),
            'location' => 'Lantai '.fake()->numberBetween(1, 2).' - Zona '.fake()->randomLetter(),
            'purchase_date' => fake()->date(),
            'purchase_price' => fake()->randomFloat(2, 5_000_000, 50_000_000),
            'price_per_play' => fake()->randomElement([2_000, 5_000, 10_000]),
            'status' => Machine::STATUS_ACTIVE,
        ];
    }

    /**
     * Machines currently being serviced.
     */
    public function maintenance(): static
    {
        return $this->state(fn () => ['status' => Machine::STATUS_MAINTENANCE]);
    }

    /**
     * Machines that are out of order.
     */
    public function broken(): static
    {
        return $this->state(fn () => ['status' => Machine::STATUS_BROKEN]);
    }

    /**
     * Machines that are no longer used.
     */
    public function retired(): static
    {
        return $this->state(fn () => ['status' => Machine::STATUS_RETIRED]);
    }
}
