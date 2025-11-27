<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
         
        $this->assertTrue(false,"Ez nem true");
    }

    public function test_theAssert_is_true(): void
    {
         $theAssert = 7 > 5;
        //Jelentése: állítom hogy amit adtak a paraméteremben, az True (assertTrue)
        $this->assertTrue( $theAssert,"Az állítás nem igaz");
    }

    public function test_check_person_map(): void
    {
        $person = [
            'name' => 'roger',
            'age' => 18,
        ];
        $age = 18;
        $key = "name";
        $name = "roger";
        $personPropertyCount = 2;
        $this->assertEquals($age, $person['age'], "Az 'age' nem $age");
        $this->assertArrayHasKey($key, $person, "A kulcs nem '$key'");
        $this->assertContains($name, $person, "Ez nem $name");
        $this->assertCount($personPropertyCount, $person, "A személy adatainak száma nem: $personPropertyCount");
        $this->assertIsArray($person,"Ez nem gyűjtemény");
        $this->assertIsString($name, "Ez nem sztring");
    }
}
