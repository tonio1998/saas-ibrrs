<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Households;
use App\Models\Address\Regions;

class ResidentSeeder extends Seeder
{
    public function run()
    {
        if (!Households::exists()) {
            $this->command->warn('No households found. Skipping ResidentSeeder.');
            return;
        }

        $faker = fake('en_US');
        $now = now();

        $regions = Regions::with('provinces.cities.barangays')->get();

        if ($regions->isEmpty()) {
            $this->command->warn('No address data found.');
            return;
        }

        $certificateTypes = DB::table('certificate_types')
            ->whereIn('id', range(1,5))
            ->pluck('fee', 'id');

        $totalHouseholds = Households::count();
        $bar = $this->command->getOutput()->createProgressBar($totalHouseholds);
        $bar->start();

        DB::beginTransaction();

        try {

            Households::select('id')->chunk(200, function ($households) use ($faker, $regions, $now, $certificateTypes, $bar) {

                foreach ($households as $household) {

                    $members = rand(3, 8);

                    for ($i = 0; $i < $members; $i++) {

                        $gender = rand(0,1) ? 'Male' : 'Female';

                        $birthDate = $faker->dateTimeBetween('-80 years', '-1 years');
                        $age = $birthDate->diff(now())->y;

                        if ($age < 18) {
                            $civilStatus = 'Single';
                            $occupation = 'Student';
                            $isVoter = 0;
                        } elseif ($age <= 60) {
                            $civilStatus = $faker->randomElement(['Single','Married']);
                            $occupation = $faker->randomElement(['Farmer','Vendor','Driver','Teacher','Employee']);
                            $isVoter = 1;
                        } else {
                            $civilStatus = $faker->randomElement(['Married','Widowed']);
                            $occupation = $faker->randomElement(['Retired','Farmer','Unemployed']);
                            $isVoter = 1;
                        }

                        $firstNAme = $faker->firstName($gender === 'Male' ? 'male' : 'female');
                        $lastNAme = $faker->lastName($gender === 'Male' ? 'male' : 'female');

                        $residentId = DB::table('residents')->insertGetId([
                            'household_id' => $household->id,
                            'FirstName' => $firstNAme,
                            'LastName' => $lastNAme,
                            'gender' => $gender,
                            'CivilStatus' => $civilStatus,
                            'Occupation' => $occupation,
                            'is_voter' => $isVoter,
                            'BirthDate' => $birthDate->format('Y-m-d'),
                            'created_at' => $now,
                            'updated_at' => $now,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'archived' => 0,
                            'status' => 'active',
                        ]);

                        $this->command->info("Resident: $firstNAme $lastNAme");

                        $region = $regions->filter(fn($r) => $r->provinces->isNotEmpty())->random();

                        $province = $region->provinces
                            ->filter(fn($p) => $p->cities->isNotEmpty())
                            ->random();

                        $city = $province->cities
                            ->filter(fn($c) => $c->barangays->isNotEmpty())
                            ->random();

                        $barangay = $city->barangays->random();

                        $unit = $faker->buildingNumber();
                        $street = $faker->streetName();
                        $purok = rand(1,5);

                        DB::table('resident_info')->insert([
                            'resident_id' => $residentId,
                            'unit' => $unit,
                            'street' => $street,
                            'purok' => $purok,
                            'barangay' => $barangay->code,
                            'city' => $city->code,
                            'province' => $province->code,
                            'region' => $region->code,
                            'zip' => rand(8000, 9999),
                            'full_address' => "{$unit}, {$street}, Purok {$purok}, {$barangay->name}, {$city->name}, {$province->name}, {$region->name}",
                            'status' => 'active',
                            'archived' => 0,
                        ]);

                        $this->command->info("\t\t Adding info of $firstNAme $lastNAme");

                        for ($r = 0; $r < 20; $r++) {

                            $requested = $faker->dateTimeBetween('-2 years', 'now');
                            $approvedAt = (clone $requested)->modify('+1 day');

                            $status = $faker->randomElement(['Pending','Approved']);

                            $certificateTypeId = rand(1,5);
                            $fee = $certificateTypes[$certificateTypeId] ?? 0;

                            $ControlNo =
                                strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' .
                                strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' .
                                strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' .
                                strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

                            $requestId = DB::table('certificate_requests')->insertGetId([
                                'ControlNo' => $ControlNo,
                                'resident_id' => $residentId,
                                'certificate_type_id' => $certificateTypeId,
                                'business_id' => rand(1,1000),
                                'purpose' => $faker->sentence(4),
                                'remark' => $status,
                                'requested_at' => $requested,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'status' => 'active',
                                'archived' => 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                            if ($status === 'Approved') {

                                DB::table('certificates')->insert([
                                    'request_id' => $requestId,
                                    'issued_by' => 1,
                                    'Fee' => $fee,
                                    'issued_at' => $approvedAt,
                                    'or_number' => rand(10000, 99999),
                                    'amount_paid' => $fee,
                                    'payment_method' => 'cash',
                                    'payment_date' => $approvedAt,
                                    'Remark' => 'Issued',
                                    'created_by' => 1,
                                    'updated_by' => 1,
                                    'status' => 'active',
                                    'archived' => 0,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                $this->command->info("\t\t\t Adding cert of $firstNAme $lastNAme");
                            }

                        }
                    }

                    $bar->advance();
                }
            });

            DB::commit();

            $bar->finish();
            $this->command->info("\nResident seeding completed.");

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
