<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Fixed machine fleet so dashboard numbers stay reproducible.
     */
    public function run(): void
    {
        // Category name => id lookup so each machine links to its category.
        $categoryIds = Category::pluck('id', 'name');

        // [category, name, serial, location, years owned, purchase price, price per play, status]
        $machines = [
            ['Racing', 'Wangan Midnight Maximum Tune', 'ARC-0001', 'Lantai 1 - Zona A', 5, 133_600_000, 16_000, Machine::STATUS_MAINTENANCE],
            ['Racing', 'Initial D Arcade Stage', 'ARC-0002', 'Lantai 1 - Zona A', 4, 38_000_000, 10_000, Machine::STATUS_ACTIVE],
            ['Racing', 'Horse Racing', 'ARC-0003', 'Lantai 1 - Zona A', 7, 76_000_000, 13_000, Machine::STATUS_ACTIVE],
            ['Racing', 'GTI Club Racer', 'ARC-0004', 'Lantai 2 - Zona C', 6, 25_000_000, 15_000, Machine::STATUS_ACTIVE],
            ['Fighting', 'Tekken 7 FR', 'ARC-0005', 'Lantai 1 - Zona B', 3, 30_000_000, 13_000, Machine::STATUS_BROKEN],
            ['Fighting', 'Street Fighter II', 'ARC-0006', 'Lantai 1 - Zona B', 10, 8_000_000, 10_000, Machine::STATUS_ACTIVE],
            ['Fighting', 'Mortal Kombat 4', 'ARC-0007', 'Lantai 1 - Zona B', 12, 46_500_000, 10_000, Machine::STATUS_RETIRED],
            ['Shooting', 'Time Crisis 4', 'ARC-0008', 'Lantai 2 - Zona D', 5, 28_000_000, 15_000, Machine::STATUS_ACTIVE],
            ['Shooting', 'House of the Dead 2', 'ARC-0009', 'Lantai 2 - Zona D', 9, 52_000_000, 18_000, Machine::STATUS_ACTIVE],
            ['Claw Machine', 'Claw Machine Jumbo', 'ARC-0010', 'Lantai 2 - Zona E', 2, 19_000_000, 10_000, Machine::STATUS_ACTIVE],
            ['Claw Machine', 'Mini Claw Pinky', 'ARC-0011', 'Lantai 2 - Zona E', 2, 1_000_000, 18_000, Machine::STATUS_ACTIVE],
            ['Rhythm', 'Maimai DX', 'ARC-0012', 'Lantai 1 - Zona F', 2, 283_000_000, 15_000, Machine::STATUS_ACTIVE],
            ['Rhythm', 'Taiko no Tatsujin', 'ARC-0013', 'Lantai 1 - Zona F', 3, 270_000_000, 15_000, Machine::STATUS_ACTIVE],
            ['Rhythm', 'Pump It Up LX', 'ARC-0014', 'Lantai 1 - Zona F', 4, 178_000_000, 15_000, Machine::STATUS_ACTIVE],
        ];

        foreach ($machines as [$categoryName, $name, $serial, $location, $yearsOwned, $purchasePrice, $pricePerPlay, $status]) {
            Machine::create([
                'category_id' => $categoryIds[$categoryName],
                'name' => $name,
                'serial_number' => $serial,
                'location' => $location,
                'purchase_date' => now()->subYears($yearsOwned)->startOfYear(),
                'purchase_price' => $purchasePrice,
                'price_per_play' => $pricePerPlay,
                'status' => $status,
            ]);
        }
    }
}
