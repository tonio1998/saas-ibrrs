<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SyncPSGC extends Command
{
    protected $signature = 'psgc:sync';
    protected $description = 'Download and store PSGC data';

    public function handle()
    {
        DB::beginTransaction();

        try {
            $this->info('Fetching regions...');
            $regions = Http::get('https://psgc.gitlab.io/api/regions/')->json();

            foreach ($regions as $region) {
                DB::table('loc_regions')->updateOrInsert(
                    ['code' => $region['code']],
                    ['name' => $region['name']]
                );

                $this->info("Region: {$region['name']}");

                $provinces = Http::get("https://psgc.gitlab.io/api/regions/{$region['code']}/provinces/")->json();

                foreach ($provinces as $province) {
                    DB::table('loc_provinces')->updateOrInsert(
                        ['code' => $province['code']],
                        [
                            'name' => $province['name'],
                            'region_code' => $region['code']
                        ]
                    );

                    $this->info("\tProvinces: {$province['name']}");


                    $cities = Http::get("https://psgc.gitlab.io/api/provinces/{$province['code']}/cities-municipalities/")->json();

                    foreach ($cities as $city) {
                        DB::table('loc_cities')->updateOrInsert(
                            ['code' => $city['code']],
                            [
                                'name' => $city['name'],
                                'province_code' => $province['code']
                            ]
                        );

                        $this->info("\t\tMunicipalities: {$city['name']}");

                        $barangays = Http::get("https://psgc.gitlab.io/api/cities-municipalities/{$city['code']}/barangays/")->json();

                        foreach ($barangays as $barangay) {
                            DB::table('loc_barangays')->updateOrInsert(
                                ['code' => $barangay['code']],
                                [
                                    'name' => $barangay['name'],
                                    'city_code' => $city['code']
                                ]
                            );
                            $this->info("\t\t\tBarangayr: {$barangay['name']}");
                        }
                    }
                }
            }

            DB::commit();
            $this->info('PSGC sync completed.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }
    }
}
