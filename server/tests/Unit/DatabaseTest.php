<?php

namespace Tests\Unit;

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
        echo PHP_EOL."\tAdatbázis -> DB_DATABASE={$databaseNameEnv} | adatbázis: {$databaseNameConn}";
    }

    public function test_sports_table_structure()
    {
        // Ellenőrizzük, hogy a tábla létezik
        $this->assertTrue(Schema::hasTable('sports'));

        // Ellenőrizzük a mezőket és azok típusait
        $this->assertTrue(Schema::hasColumn('sports', 'id'));
        $this->assertTrue(Schema::hasColumn('sports', 'sportNev'));
        $this->assertEquals('bigint', Schema::getColumnType('sports', 'id'));
        //dd(Schema::getColumnType('sports', 'sportNev'));
        $this->assertEquals('varchar', Schema::getColumnType('sports', 'sportNev'));

        $this->assertTrue(Schema::hasColumn('sports', 'id'));

        //Elsődleges kulcs
        $indexes = DB::select('SHOW INDEX FROM sports');
        $primaryIndex = collect($indexes)->firstWhere('Key_name', 'PRIMARY');
        $this->assertNotNull($primaryIndex);

    }

}
