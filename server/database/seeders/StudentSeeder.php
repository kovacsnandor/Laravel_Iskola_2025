<?php

namespace Database\Seeders;

use App\Models\Schoolclass;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avgClassSize = 28;
        $numberOfClasses= Schoolclass::count();
        $numberOfStudent = $numberOfClasses*$avgClassSize;
        Student::factory()->count($numberOfStudent)->create();
    }
}
