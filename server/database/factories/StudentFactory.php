<?php

namespace Database\Factories;

use App\Models\Schoolclass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected function withFaker()
    {
        // Manuális beállítás az app config felülírására
        return \Faker\Factory::create('hu_HU');
    }


    public static function getScholarShip(float $averageGrade): int
    {

        $scholarshipTiers = [
            "4.5" => 42000,
            "3.5" => 25000,
            "2.5" => 16000,
            "2.0" => 8000,
        ];

        $scholarshipAmount = 0;

        // Az 5.0-ás átlag külön kezelése a maximális díj miatt
        if ($averageGrade >= 5.0) {
            return 60000;
        }

        foreach ($scholarshipTiers as $minAverage => $amount) {
            if ($averageGrade >= (float)$minAverage) {
                // Megtaláltuk a legmagasabb szintet, amibe beleesik
                $scholarshipAmount = $amount;
                break;
            }
        }
        return $scholarshipAmount; // 0 Ft-ot ad vissza 2.00 alatti átlag esetén
    }



// Használat:
// $grade = $faker->randomFloat(1, 1.0, 5.0); // Pl. 4.7
// $scholarship = getScholarship($grade);      // Eredmény: 22000

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $neme = $this->faker->boolean();
        $lastName = $this->faker->lastName();
        if ($neme) {
            //férfi
            $firstName = $this->faker->firstName('male');
        } else {
            //nő
            $firstName = $this->faker->firstName('female');
        }
        $diakNev = "$lastName $firstName";
        //Véletlen osztály
        $randomClass = Schoolclass::inRandomOrder()->first();
        $grade = substr($randomClass->osztalyNev, 0, 1);
        $age =$grade +5;
        $schoolclassId = $randomClass->id;
        $iranyitoszam = $this->faker->postcode();
        $lakHelyseg = $this->faker->city();
        $lakCim = $this->faker->streetAddress();
        $szulHelyseg = $this->faker->city();
        $szulDatum = $this->faker->dateTimeBetween('-'.($age+1).' years', '-'.$age.' years');
        $igazolvanyszam = $this->faker->bothify('??#######');
        $atlag = $this->faker->randomFloat(
            $maxDecimals = 1,
            $min = 1.0,
            $max = 5.0
        );
        $osztondij = self::getScholarship($atlag);
        return [
            'diakNev' => $diakNev,
            'schoolclassId' => $schoolclassId,
            'neme' => $neme,
            'iranyitoszam' => $iranyitoszam,
            'lakHelyseg' => $lakHelyseg,
            'lakCim' => $lakCim,
            'szulHelyseg' => $szulHelyseg,
            'szulDatum' => $szulDatum,
            'igazolvanyszam' => $igazolvanyszam,
            'atlag' => $atlag,
            'osztondij' => $osztondij,
        ];
    }
}
