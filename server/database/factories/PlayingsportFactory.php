<?php

namespace Database\Factories;

use App\Models\Sport;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Playingsport>
 */
class PlayingsportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomStudentId = Student::inRandomOrder()->first();   
        $randomSportId = Sport::inRandomOrder()->first();   
        return [
            'studentId' => $randomStudentId,
            'sportId' => $randomSportId,
        ];
    }
}
