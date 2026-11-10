<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run()
    {
        $campuses = [
            ['name' => 'KNUST Kumasi Campus', 'slug' => 'knust-kumasi-campus', 'latitude' => 6.673175, 'longitude' => -1.565423],
            ['name' => 'KNUST Obuasi Campus', 'slug' => 'knust-obuasi-campus', 'latitude' => 6.197980, 'longitude' => -1.665120],
            ['name' => 'Kumasi Technical University (KSTU)', 'slug' => 'kumasi-technical-university', 'latitude' => 6.691110, 'longitude' => -1.610000],
            ['name' => 'University of Ghana, Legon', 'slug' => 'university-of-ghana', 'latitude' => 5.650830, 'longitude' => -0.186940],
            ['name' => 'USTED (AAMUSTED), Tanoso Campus', 'slug' => 'kumasi-aamusted', 'latitude' => 6.696900, 'longitude' => -1.681300],
            ['name' => 'Catholic University of Ghana, Fiapre', 'slug' => 'catholic-university-ghana-fiapre', 'latitude' => 7.353250, 'longitude' => -2.386690],
            ['name' => 'UG Kumasi City Campus', 'slug' => 'ug-kumasi-city', 'latitude' => 6.683000, 'longitude' => -1.600000],
        ];

        foreach ($campuses as $campus) {
            Campus::updateOrCreate(['slug' => $campus['slug']], $campus);
        }
    }
}