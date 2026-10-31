<?php

namespace Database\Seeders;

use App\Models\IncomeRecord;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncomeRecordSeeder extends Seeder
{
    /**
     * Monthly income per machine for the last 4 months, computed
     * deterministically (play count formula, not random) so the
     * dashboard totals are stable between reseeds.
     */
    public function run(): void
    {
        $machines = Machine::all();
        $budi = User::where('email', 'staff1@gmail.com')->value('id');
        $sari = User::where('email', 'staff2@gmail.com')->value('id');

        foreach ($machines as $index => $machine) {
            foreach (range(3, 0) as $monthsAgo) {
                // A retired machine no longer earns income on the floor.
                if ($machine->status === Machine::STATUS_RETIRED && $monthsAgo === 0) {
                    continue;
                }

                // Fixed formula: busy machines and recent months earn more.
                $playCount = 40 + ($index * 7) + ((3 - $monthsAgo) * 12);

                // user_id is not mass assignable (ownership guard), so the
                // seeder assigns it directly — app input never can.
                $record = new IncomeRecord([
                    'machine_id' => $machine->id,
                    // Start from the 1st of the month so subtracting months
                    // never overflows (e.g. Mar 31 - 1 month). Past months
                    // close at month end; the running month is recorded as
                    // of today so dashboard ranges include it.
                    'date' => now()->startOfMonth()->subMonths($monthsAgo)->endOfMonth()->min(now()),
                    'play_count' => $playCount,
                    'total_income' => $playCount * (float) $machine->price_per_play,
                ]);
                $record->user_id = $index % 2 === 0 ? $budi : $sari;
                $record->save();
            }
        }
    }
}
