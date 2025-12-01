<?php

namespace Tests\Unit;
use Database\Factories\StudentFactory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
// Ha a metódus védett (protected) volt és egy Factory-ban van,
// akkor az osztályt kell használni. Feltételezzük, hogy public vagy protected
// (és Reflection-nel tesztelnéd, de itt public-ot feltételezünk a könnyebb teszteléshez).
// Ha a Factory-n kívülre van kiemelve segédmetódusnak, akkor a megfelelő osztályt kell használni.
// Itt egy Dummy osztályt hozunk létre a tesztelés kedvéért,
// vagy importáljuk a valós StudentFactory-t, ha lehetséges.

// A teszteléshez hozunk létre egy egyszerűsített Dummy osztályt, 
// amely csak a tesztelni kívánt metódust tartalmazza.
class ScholarshipCalculator
{
    public function getScholarship(float $averageGrade): int
    {
        $scholarshipTiers = [
            "4.5" => 30000,
            "4.0" => 22000,
            "3.0" => 15000,
            "2.0" => 10000,
        ];

        $scholarshipAmount = 0;

        // Az 5.0-ás átlag külön kezelése a maximális díj miatt
        if ($averageGrade >= 5.0) {
            return 40000;
        }

        foreach ($scholarshipTiers as $minAverage => $amount) {
            if ($averageGrade >= $minAverage) {
                // Megtaláltuk a legmagasabb szintet, amibe beleesik
                $scholarshipAmount = $amount;
                break;
            }
        }

        return $scholarshipAmount; // 0 Ft-ot ad vissza 4.00 alatti átlag esetén

    }
}


class ScholarshipCalculatorTest extends TestCase
{
    protected ScholarshipCalculator $calculator;

    // A teszt futása előtt inicializáljuk az osztályt
    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new ScholarshipCalculator();
        
    }

    // --- Sikeres (pozitív) tesztesetek ---

    /**
     * Teszteli az 5.0 vagy afölötti átlagot.
     */
    #[DataProvider('averageGradesForMaxScholarship')]
    public function test_it_returns_max_scholarship_for_5_dot_0_and_above(float $average)
    {
        $this->assertEquals(40000, $this->calculator->getScholarship($average));
    }

    public static function averageGradesForMaxScholarship(): array
    {
        return [
            [5.0],
            [5.0], // Ismétlés a kerekítési pontosság miatt
            [5.01],
        ];
    }

    /**
     * Teszteli a 4.5-ös szintet.
     */
    #[DataProvider('averageGradesFor30000')]
    public function test_it_returns_30000_for_4_dot_5_to_4_dot_99(float $average)
    {
        // $this->assertEquals(30000, $this->calculator->getScholarship($average));
        $this->assertEquals(30000, StudentFactory::getScholarship($average));
    }

    public static function averageGradesFor30000(): array
    {
        return [
            [4.5], // A pontos határ
            [4.99],
            [4.501],
        ];
    }

    /**
     * Teszteli a 4.0-ás szintet.
     */
    #[DataProvider('averageGradesFor22000')]
    public function test_it_returns_22000_for_4_dot_0_to_4_dot_49(float $average)
    {
        $this->assertEquals(22000, $this->calculator->getScholarship($average));
    }

    public static function averageGradesFor22000(): array
    {
        return [
            [4.0], // A pontos határ
            [4.49],
            [4.001],
        ];
    }

    /**
     * Teszteli a 3.0-ás szintet.
     */
    #[DataProvider('averageGradesFor15000')]
    public function test_it_returns_15000_for_3_dot_0_to_3_dot_99(float $average)
    {
        $this->assertEquals(15000, $this->calculator->getScholarship($average));
    }

    public static function averageGradesFor15000(): array
    {
        return [
            [3.0],
            [3.99],
            [3.5],
        ];
    }

    /**
     * Teszteli a 2.0-ás szintet.
     */
    #[DataProvider('averageGradesFor10000')]
    public function test_it_returns_10000_for_2_dot_0_to_2_dot_99(float $average)
    {
        $this->assertEquals(10000, $this->calculator->getScholarship($average));
    }

    public static function averageGradesFor10000(): array
    {
        return [
            [2.0],
            [2.99],
            [2.5],
        ];
    }

    // --- Hiba/Negatív tesztesetek ---

    /**
     * Teszteli, hogy 2.0 alatti átlag esetén 0-t ad-e vissza.
     * 
     */
    #[DataProvider('averageGradesForZeroScholarship')]
    public function test_it_returns_zero_for_below_2_dot_0(float $average)
    {
        $this->assertEquals(0, $this->calculator->getScholarship($average));
    }

    public static function averageGradesForZeroScholarship(): array
    {
        return [
            [1.9],
            [0.0],
            [1.0],
        ];
    }
}
