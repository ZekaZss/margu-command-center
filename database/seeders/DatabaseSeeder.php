<?php

namespace Database\Seeders;

use App\Models\Vessel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kapal 1: Dimulai dengan status AMAN
        Vessel::create([
            'device_code' => 'MARGU-SEC-001',
            'latitude' => 1.18000000,
            'longitude' => 104.10000000,
            'status' => 'SECURE', 
        ]);

        // Kapal 2: Target Simulator kita (Dimulai dengan AMAN)
        Vessel::create([
            'device_code' => 'MARGU-SEC-002',
            'latitude' => 1.12000000,
            'longitude' => 104.05000000,
            'status' => 'SECURE',
        ]);

        // Kapal 3: Patroli TNI AL
        Vessel::create([
            'device_code' => 'MARGU-TNI-003',
            'latitude' => 1.05000000,
            'longitude' => 103.95000000,
            'status' => 'PATROL',
        ]);

        // Kapal 4: Offline
        Vessel::create([
            'device_code' => 'MARGU-OFF-004',
            'latitude' => 1.15000000,
            'longitude' => 104.08000000,
            'status' => 'OFFLINE',
        ]);
    }
}