<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Puroks;

class HouseholdSeeder extends Seeder
{
    public function run()
    {
        $puroks = Puroks::pluck('id');

        if ($puroks->isEmpty()) {
            $this->command->warn('No puroks found. Skipping HouseholdSeeder.');
            return;
        }

        $year = now()->year;
        $now = now();

        $faker = fake();
        $batch = [];
        $batchSize = 1000;

        $counter = 1;

        for ($i = 0; $i < 1000; $i++) {

            $batch[] = [
                'household_code' => 'HH-' . $year . '-' . str_pad($counter++, 6, '0', STR_PAD_LEFT),
                'purok_id' => $puroks->random(),
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
                'archived' => 0,
                'status' => 'active',
            ];

            if (count($batch) >= $batchSize) {
                DB::table('households')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('households')->insert($batch);
        }
    }
}
