<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceLog>
 */
class MaintenanceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'machine_id' => Machine::factory(),
            'user_id' => User::factory(),
            'issue' => fake()->randomElement([
                'Joystick kanan tidak merespons',
                'Layar mendadak mati',
                'Koin tertelan tetapi permainan tidak mulai',
                'Suara speaker berdesis',
                'Tombol start macet',
            ]),
            'action_taken' => fake()->optional(0.7)->sentence(),
            'cost' => fake()->randomFloat(2, 0, 1_500_000),
            'status' => MaintenanceLog::STATUS_OPEN,
            'date' => fake()->date(),
        ];
    }

    /**
     * Logs whose issue has been resolved.
     */
    public function done(): static
    {
        return $this->state(fn () => ['status' => MaintenanceLog::STATUS_DONE]);
    }
}
