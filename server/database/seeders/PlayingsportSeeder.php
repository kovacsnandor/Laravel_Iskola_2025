<?php

namespace Database\Seeders;

use App\Models\Playingsport;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayingsportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $percentageOfStudentsPlayingSports = 0.2;
        $averageNumberOfSportsAStudentPlays = 1.3;

        $numberOfStudent = Student::count();
        $numberOfAthletes = round($numberOfStudent * $percentageOfStudentsPlayingSports);
        $numberOfSports = round($numberOfAthletes * $averageNumberOfSportsAStudentPlays);
        Playingsport::factory()->count($numberOfSports)->create();
    }
}
