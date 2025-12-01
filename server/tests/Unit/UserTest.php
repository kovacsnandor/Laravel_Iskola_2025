<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected $expectedSchema = [
        'id'         => 'bigint',
        'name'       => 'varchar',
        'email'      => 'varchar',
        'password'   => 'varchar',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    //Van-e ilyen tábla
    public function test_exists_users_table(){
        // Ellenőrizzük, hogy a tábla létezik
        $this->assertTrue(Schema::hasTable('users'));
    }

    //Megvan-e az összes mező
    public function test_does_the_user_table_contain_all_fields()
    {
        //A felsorolt mezők megvannak-e
        foreach ($this->expectedSchema as $column => $type) {
            # code...
            $this->assertTrue(Schema::hasColumn('users', $column), "A '$column' oszlop nem található a 'users' táblában.");
        }
    }


    //A user tábla oszlopainak és típusainak ellenőrzése
    public function test_the_user_table_columns_have_the_expected_types()
    {
        // Lekérdezzük a tábla aktuális oszlopait
        $columns = Schema::getColumnListing('users');

        // Ellenőrizzük, hogy minden várt mező létezik-e
        $this->assertEmpty(
            array_diff(array_keys($this->expectedSchema), $columns),
            'Hiányzó oszlopok a felhasználók táblában.'
        );

        // Végigiterálunk a várt sémán
        foreach ($this->expectedSchema as $columnName => $expectedLaravelType) {

            // Lekérdezzük az adatbázis oszloptípusát (ez a módszer eltérhet DB-nként)
            // MySQL-nél a getColumnType() metódus a legmegbízhatóbb:
            $actualDbSqlType = Schema::getColumnType('users', $columnName);
            
            //Összehasonlítjuk az aktuális típust a várt típussal
            $isTypeMatch = $actualDbSqlType == $expectedLaravelType;
            $this->assertTrue(
                $isTypeMatch,
                "A '{$columnName}' oszlop típusa nem egyezik. Várt: '{$expectedLaravelType}', Kapott DB-típus: '{$actualDbSqlType}'."
            );
        }
    }


    //Megvan-e a user id alapján?
    public function test_check_if_users_getting_fetched_with_id(): void
    {
        $response = DB::table("users")->find(1);
        // $response = User::find(3);
        //dd($response);
        //dd($response->id);
        //Adott mező értékének ellenőrzése
        $this->assertEquals(1, $response->id);
    }

    //A users tábla rekorjainak száma
    function test_users_table_record_number()
    {

        //A rekordok számának ellenőrzése
        $response = DB::table("users")->get();
        // dd($response);
        //A userek száma 3-e
        $this->assertCount(3, $response);
        //A rekordok száma > mint 0
        $this->assertGreaterThan(0, count($response));
    }


    //Létezik-e a user?
    function test_does_the_user_exist()
    {
        $email = "admin@example.com";
        //1. módszer
        $this->assertDatabaseHas('users', ['email' => $email]);

        //2. módszer
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);

        //3. módszer
        $this->assertTrue(
            DB::table('users')
                ->where('email', $email)
                ->exists()
        );
    }

    //Jelszó ellenőrzés
    public function test_a_given_password_matches_the_users_hashed_password()
    {
        //A nyers jelszó
        $rawPassword = '123';
        $email = "admin@example.com";
        //Megkeressük a usert email alapján
        $user = User::where('email', $email)->first();

        // 2. Ellenőrizze a jelszót
        // A Hash::check() metódus összehasonlítja a nyers (raw) jelszót 
        // a hashelt (hashed) változattal, és true/false-t ad vissza.
        $passwordMatches = Hash::check($rawPassword, $user->password);

        // 3. Végezze el az asserciót (ellenőrzést)
        $this->assertTrue($passwordMatches, "Nem ez a jelszó: $rawPassword");

        // 4. Egy sikertelen próba
        $rawPassword = 'wrong-password';
        $this->assertFalse(
            Hash::check($rawPassword, $user->password),
            "Nem ennek kéne a jelszónak lennie: $rawPassword"
        );
    }
}
