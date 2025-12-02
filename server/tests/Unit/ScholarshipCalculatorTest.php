<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Database\Factories\StudentFactory; // Feltételezett útvonal. Cserélje ki a megfelelőre!
use PHPUnit\Framework\Attributes\DataProvider;

class ScholarshipCalculatorTest extends TestCase
{
    // A tesztelt osztály egy példánya.
    // Létrehozható a setUp() metódusban, ha minden tesztnek szüksége van rá.
    // protected StudentFactory $studentFactory;

    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     // Példányosítjuk az osztályt a tesztek futtatása előtt
    //     $this->studentFactory = new StudentFactory();
    // }

    /**
     * Adat szolgáltató a tesztesetekhez az új szintekkel.
     *
     * @return array
     */
    
    public static function scholarshipDataProvider(): array
    {
        return [
            // Átlag => Várható ösztöndíj összege
            'Max ösztöndíj - pontos 5.0' => [5.0, 60000],
            'Max ösztöndíj - 5.0 felett' => [5.05, 60000],

            // 4.5-ös szint (42000 Ft)
            '4.5 szint - pontos 4.5' => [4.5, 42000],
            '4.5 szint - 4.5 és 5.0 között' => [4.99, 42000],

            // 3.5-ös szint (25000 Ft)
            '3.5 szint - pontos 3.5' => [3.5, 25000],
            '3.5 szint - 3.5 és 4.5 között' => [4.49, 25000],

            // 2.5-ös szint (16000 Ft)
            '2.5 szint - pontos 2.5' => [2.5, 16000],
            '2.5 szint - 2.5 és 3.5 között' => [3.49, 16000],

            // 2.0-ás szint (8000 Ft)
            '2.0 szint - pontos 2.0' => [2.0, 8000],
            '2.0 szint - 2.0 és 2.5 között' => [2.49, 8000],

            // 0 Ft (minimális átlag alatt)
            'Minimum alatt - 1.99' => [1.99, 0],
            'Minimum alatt - 0.0' => [0.0, 0],
        ];
    }

    /**
     * //@dataProvider scholarshipDataProvider
     */
    #[DataProvider('scholarshipDataProvider')]
    public function test_get_scholarship_returns_correct_amount_for_average_grade(float $averageGrade, int $expectedAmount): void
    {
        // Act
        // A nem statikus metódust az osztály példányán keresztül hívjuk meg
        // $actualAmount = $this->studentFactory->getScholarShip($averageGrade);
        $actualAmount = StudentFactory::getScholarShip($averageGrade);

        // Assert
        $this->assertEquals($expectedAmount, $actualAmount, "Az ösztöndíj összege hibás az {$averageGrade} átlag esetén.");
    }
}