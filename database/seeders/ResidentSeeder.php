<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Households;

class ResidentSeeder extends Seeder
{
    public function run()
    {
        if (!Households::exists()) {
            $this->command->warn('No households found. Skipping ResidentSeeder.');
            return;
        }

        $genders = ['Male','Male','Male','Female','Female'];
        $civilStatus = ['Single','Married','Married','Widowed'];
        $occupations = ['Farmer','Student','Vendor','Driver','Teacher','Unemployed'];

        $faker = fake();
        $batch = [];
        $batchSize = 9500;
        $now = now();

        DB::beginTransaction();

        try {

            Households::select('id')->chunk(500, function ($households) use (
                &$batch,
                $batchSize,
                $faker,
                $genders,
                $civilStatus,
                $occupations,
                $now
            ) {

                foreach ($households as $household) {

                    $members = rand(3, 12);

                    for ($i = 0; $i < $members; $i++) {

                        $batch[] = [
                            'household_id' => $household->id,
                            'FirstName' => $faker->firstName(),
                            'LastName' => $faker->lastName(),
                            'gender' => $genders[array_rand($genders)],
                            'CivilStatus' => $civilStatus[array_rand($civilStatus)],
                            'Occupation' => $occupations[array_rand($occupations)],
                            'created_at' => $now,
                            'updated_at' => $now,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'archived' => 0,
                            'status' => 'active',
                        ];

                        if (count($batch) >= $batchSize) {
                            DB::table('residents')->insert($batch);
                            $batch = [];
                        }
                    }
                }
            });

            if (!empty($batch)) {
                DB::table('residents')->insert($batch);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
