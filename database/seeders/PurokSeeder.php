<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puroks;
use Illuminate\Support\Facades\Auth;

class PurokSeeder extends Seeder
{
    public function run()
    {
        $suffix = [
            'San Isidro','Mabini','Rizal','Bonifacio','Pag-asa',
            'Malaya','Kalinaw','Masagana','Bagong Silang','Sampaguita'
        ];

        $data = [];

        for ($i = 1; $i <= 15; $i++) {
            $data[] = [
                'PurokNo' => $i,
                'PurokName' => 'Purok ' . $i . ' - ' . $suffix[array_rand($suffix)],
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 1,
                'updated_by' => 1,
                'archived' => 0,
                'status' => 'active',
            ];
        }

        Puroks::insert($data);
    }
}
