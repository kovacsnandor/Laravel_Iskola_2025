<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $fileName = 'csv\sports.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);

        if (Sport::count() === 0) {
            Sport::factory()->createMany($data);
        }
    }
}
