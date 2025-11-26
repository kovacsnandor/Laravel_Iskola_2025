<?php

namespace Tests\Unit;

use App\Models\Schoolclass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;


class DatabaseTest extends TestCase
{
    //Bármit változtatunk az adatbázison a teszt során, az vissza lesz vonva
    use DatabaseTransactions;


    public function test_database_creation_end_tables_exists()
    {
        $databaseNameConn = DB::connection()->getDatabaseName();
        //dd($databaseNameConn);
        $databaseNameEnv = env('DB_DATABASE');
        //dd($databaseNameConn == $databaseNameEnv);
        //Az adatbázis ellenőrzése
        $this->assertEquals($databaseNameConn, $databaseNameEnv);
        //Táblák létezésének 
        $this->assertDatabaseHas('students');
        $this->assertDatabaseHas('schoolclasses');
        $this->assertDatabaseHas('playingsports');
        $this->assertDatabaseHas('sports');
        $this->assertDatabaseHas('users');
        echo PHP_EOL . "\tAdatbázis -> DB_DATABASE={$databaseNameEnv} | adatbázis: {$databaseNameConn}";
    }

    public function test_sports_table_structure()
    {
        // Ellenőrizzük, hogy a tábla létezik
        $this->assertTrue(Schema::hasTable('sports'));

        // Ellenőrizzük a mezőket és azok típusait
        $this->assertTrue(Schema::hasColumn('sports', 'id'));
        $this->assertTrue(Schema::hasColumn('sports', 'sportNev'));
        $this->assertEquals('bigint', Schema::getColumnType('sports', 'id'), "sports: Nem bigint az id típusa");
        //dd(Schema::getColumnType('sports', 'sportNev'));
        $this->assertEquals('varchar', Schema::getColumnType('sports', 'sportNev'));

        $this->assertTrue(Schema::hasColumn('sports', 'id'));

        //Elsődleges kulcs
        $indexes = DB::select('SHOW INDEX FROM sports');
        $primaryIndex = collect($indexes)->firstWhere('Key_name', 'PRIMARY');
        $this->assertNotNull($primaryIndex, "Nincs elsődleges kulcs");

        //Egyedi index: sportNev
        $uniqueIndex = collect($indexes)->firstWhere('Key_name', 'sports_sportnev_unique');

        $this->assertNotNull($uniqueIndex, "Hiányzik az egyedi index a 'sports.sportNev' oszlopon.");
    }


    public function test_students_schoolclasses_relationships()
    {

        //A diák tábla kapcsolatai
        $tableName = "students";
        $foreignKeyName = "schoolclassId";
        $databaseName = env('DB_DATABASE');
        $contstraint_name = "PRIMARY";

        $query = "
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                information_schema.KEY_COLUMN_USAGE
            WHERE
                TABLE_NAME = ? and COLUMN_NAME = ? and CONSTRAINT_SCHEMA = ? and CONSTRAINT_NAME <> ?";

        $rows = DB::select($query, [$tableName, $foreignKeyName, $databaseName, $contstraint_name]);
        // dd($rows);
        //Idegen kulcs neve: osztalyId
        $this->assertEquals('schoolclassId', $rows[0]->COLUMN_NAME);
        //Referencia tábla neve: osztalies
        $this->assertEquals('schoolclasses', $rows[0]->REFERENCED_TABLE_NAME);
        //Referencia oszlop neve: id
        $this->assertEquals('id', $rows[0]->REFERENCED_COLUMN_NAME);


        //Készítünk egy osztályt
        $dataSchoolclass =
            [
                'osztalyNev' => '99.d'
            ];
        $schoolclass = Schoolclass::factory()->create($dataSchoolclass);

        //Az új osztállyal készítek egy diákot
        $dataStudent =
            [
                'diakNev' => 'Rudi',
                'schoolclassId' => $schoolclass->id,
                'neme' => true,
                'iranyitoszam' => '1234',
                'lakHelyseg' => 'Szolnok',
                'lakCim' => 'Virág utca 5.',
                'szulHelyseg' => 'Szolnok',
                'szulDatum' => '2018-01-12',
                'igazolvanyszam' => 'ab1234567',
                'atlag' => 3.5,
                'osztondij' => 5000
            ];
        $student = Student::factory()->create($dataStudent);

        //visszakeressük a diákot
        $student = DB::table('students')
            ->where('id',  $student->id)
            ->first();

        //A megtalált diák osztalyId-je megegyezik a új osztály id-jével        
        $this->assertEquals($schoolclass->id, $student->schoolclassId);
        // dd($diak);

    }
}
