<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaintenanceLogSeeder extends Seeder
{
    /**
     * Ten maintenance records, mixed open/done, spread over recent months.
     */
    public function run(): void
    {
        $machines = Machine::pluck('id', 'serial_number');
        $budi = User::where('email', 'staff1@gmail.com')->value('id');
        $sari = User::where('email', 'staff2@gmail.com')->value('id');

        // [serial, issue, action taken, cost, status, months ago]
        $logs = [
            ['ARC-0001', 'Gas Cabinet 1 kadang stuck', 'Bongkar untuk di cek mendalam', 450_000, MaintenanceLog::STATUS_DONE, 4],
            ['ARC-0005', 'Layar mendadak mati saat dimainkan', 'Cek kabel VGA, solder ulang konektor', 350_000, MaintenanceLog::STATUS_DONE, 3],
            ['ARC-0012', 'Button 4 P1 macet', 'Deep cleaning mesin dan apply lube pada tombol', 0, MaintenanceLog::STATUS_DONE, 2],
            ['ARC-0010', 'Hadiah mulai menipis', 'Isi ulang hadiah boneka', 275_000, MaintenanceLog::STATUS_DONE, 2],
            ['ARC-0008', 'Light gun player 1 tidak akurat', 'Kalibrasi ulang sensor', 50_000, MaintenanceLog::STATUS_DONE, 1],
            ['ARC-0001', 'Cabinet 3 gear shift nyangkut', null, 0, MaintenanceLog::STATUS_OPEN, 1],
            ['ARC-0013', 'Stik untuk P2 patah 1', null, 0, MaintenanceLog::STATUS_OPEN, 1],
            ['ARC-0009', 'Karti berhasil tap tapi credit ga ke-input', 'Ganti mesin tap kartu', 100_000, MaintenanceLog::STATUS_DONE, 0],
            ['ARC-0005', 'Tombol P1 kadang ga ke input', null, 0, MaintenanceLog::STATUS_OPEN, 0],
            ['ARC-0002', 'Pedal gas player 1 macet', 'Bongkar untuk dicek', 125_000, MaintenanceLog::STATUS_DONE, 0],
        ];

        foreach ($logs as $index => [$serial, $issue, $actionTaken, $cost, $status, $monthsAgo]) {
            MaintenanceLog::create([
                'machine_id' => $machines[$serial],
                // Staff alternate as the reporting technician.
                'user_id' => $index % 2 === 0 ? $budi : $sari,
                'issue' => $issue,
                'action_taken' => $actionTaken,
                'cost' => $cost,
                'status' => $status,
                'date' => now()->subMonths($monthsAgo)->startOfMonth()->addDays($index * 2),
            ]);
        }
    }
}
