<?php

namespace Tests\Feature;

use App\Models\Playingsport;
use App\Models\Schoolclass;
use App\Models\Sport;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestBase;

class UserAdminTest extends TestBase
{
    use DatabaseTransactions;

    private static $dataUser = [
        "name" =>  "Tanuló3",
        "email" => "tanulo3@example.com",
        "password" => "123"
    ];

    private static $dataSchoolclass = [
        "osztalyNev" =>  "8d",
    ];
    private static $dataSport = [
        "sportNev" =>  "Táj csí1",
    ];

    private static $dataStudent = [
        "diakNev" => "Tóth Márton",
        "schoolclassId" => 3,
        "neme" => true,
        "iranyitoszam" => "4024",
        "lakHelyseg" => "Debrecen",
        "lakCim" => "Kossuth utca 18.",
        "szulHelyseg" => "Nyíregyháza",
        "szulDatum" => "2007-11-23",
        "igazolvanyszam" => "AB123456",
        "atlag" => 3.7,
        "osztondij" => 25000
    ];

    private static $dataPlayngSport = [
        "studentId" => 1,
        "sportId" => 8
    ];


    //data provider
    public static function tablesGetDataProvider(): array
    {
        return [
            'get users admin' => ['users', 'admin@example.com', '123', 200],
            'get usersme admin' => ['usersme', 'admin@example.com', '123', 200],
            'get sports admin' => ['sports', 'admin@example.com', '123', 200],
            'get schoolclasses admin' => ['schoolclasses', 'admin@example.com', '123', 200],
            'get playingsports admin' => ['playingsports', 'admin@example.com', '123', 200],
            'get students admin' => ['students', 'admin@example.com', '123', 200],

            'get users tanar' => ['users', 'tanar@example.com', '123', 403],
            'get usersme tanar' => ['usersme', 'tanar@example.com', '123', 200],
            'get sports tanar' => ['sports', 'tanar@example.com', '123', 200],
            'get schoolclasses tanar' => ['schoolclasses', 'tanar@example.com', '123', 200],
            'get students tanar' => ['students', 'tanar@example.com', '123', 200],
            'get playingsports tanar' => ['playingsports', 'tanar@example.com', '123', 200],

            'get users diak1' => ['users', 'diak@example.com', '123', 403],
            'get usersme diak1' => ['usersme', 'diak@example.com', '123', 200],
            'get sports diak1' => ['sports', 'diak@example.com', '123', 200],
            'get schoolclasses diak1' => ['schoolclasses', 'diak@example.com', '123', 200],
            'get students diak1' => ['students', 'diak@example.com', '123', 200],
            'get playingsports diak1' => ['playingsports', 'diak@example.com', '123', 200],
        ];
    }

    public static function tablesPostDeleteDataProvider(): array
    {
        return [
            'post-delete users admin' => ['users', 'admin@example.com', '123', true, true, self::$dataUser],
            'post-delete sports admin' => ['sports', 'admin@example.com', '123', true, true, self::$dataSport],
            'post-delete schoolclasses admin' => ['schoolclasses', 'admin@example.com', '123', true, true, self::$dataSchoolclass],
            'post-delete students admin' => ['students', 'admin@example.com', '123', true, true, self::$dataStudent],
            'post-delete playingsports admin' => ['playingsports', 'admin@example.com', '123', true,true, self::$dataPlayngSport],

            'post-delete users tanar' => ['users', 'tanar@example.com', '123', true, false, self::$dataUser],
            'post-delete sports tanar' => ['sports', 'tanar@example.com', '123', false, false, self::$dataSport],
            'post-delete schoolclasses tanar' => ['schoolclasses', 'tanar@example.com', '123', false, false, self::$dataSchoolclass],
            'post-delete students tanar' => ['students', 'tanar@example.com', '123', true, true, self::$dataStudent],
            'post-delete playingsports tanar' => ['playingsports', 'tanar@example.com', '123', false,false, self::$dataPlayngSport],

            'post-delete users diak1' => ['users', 'diak@example.com', '123', true, false, self::$dataUser],
            'post-delete sports diak1' => ['sports', 'diak@example.com', '123', false, false, self::$dataSport],
            'post-delete schoolclasses diak1' => ['schoolclasses', 'diak@example.com', '123', false, false, self::$dataSchoolclass],
            'post-delete students diak1' => ['students', 'diak@example.com', '123', false, false, self::$dataStudent],
            'post-delete playingsports diak1' => ['playingsports', 'diak@example.com', '123', false,false, self::$dataPlayngSport],
        ];
    }


    #[DataProvider('tablesGetDataProvider')]
    public function test_table_user_login_get_logout($table, $email, $password, $expectedStatus): void
    {
        //login
        # code...
        $response = $this->login($email, $password);
        $response->assertStatus(200);
        //get token
        $token = $this->myGetToken($response);

        $uri = "/api/$table";
        $response = $this->myGet($uri, $token);
        $response->assertStatus($expectedStatus);

        //logout
        $response = $this->logout($token);
        $response->assertStatus(200);
    }

    #[DataProvider('tablesPostDeleteDataProvider')]
    public function test_table_user_login_post_delete_logout($table, $email, $password, $updateAccess, $deleteAccess, $data): void
    {
        //login

        # code...
        $response = $this->login($email, $password);
        $response->assertStatus(200);
        //get token
        $token = $this->myGetToken($response);

        $uri = "/api/$table";
        $response = $this->myPost($uri, $data, $token);
        if ($updateAccess) {
            # code...
            $response->assertSuccessful();
        } else {
            $response->assertClientError();
        }

        if ($table == "sports") {
            $response = Sport::factory()->create([
                "sportNev" => "Táj csí 3"
            ]);
        } else if ($table == 'users') {
            $response = User::factory()->create();
        } else if ($table == 'schoolclasses') {
            $response = Schoolclass::factory()->create([
                "osztalyNev" => "8x"
            ]);
        } else if ($table == 'students') {
            $response = Student::factory()->create();
        } else if ($table == 'playingsports') {
            $response = Playingsport::factory()->create();
        }

        //dd($response);

        $id = $response->id;
        $uri = $uri . "/$id";
        $response = $this->myDelete($uri, $token);
        if ($deleteAccess) {
            # code...
            $response->assertSuccessful();
        } else {
            $response->assertClientError();
        }

        //logout
        $response = $this->logout($token);
        $response->assertStatus(200);
    }
}
