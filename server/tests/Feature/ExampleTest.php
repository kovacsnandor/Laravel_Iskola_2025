<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestBase;

class ExampleTest extends TestBase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        //Endpoint pingelése
        $response = $this->get('/api/x');
        // $response->dumpHeaders();
 
        // $response->dumpSession();
        // $response->dumpSession();
 
        // $response->dump();

        $response->assertStatus(200);
        $response->assertSee("API");
    }

    public function test_user_login_logout()
    {
        // A login metódust már tudod hívni a $this-en keresztül:
        $response = $this->login();

        $response->assertStatus(200);
        $response->assertSuccessful();

        // $token = $response->json('data')['token'];
        $token = $this->myGetToken($response);

        //Lekérjük a user tábla adatait
        $uri = '/api/users';
        $response = $this->myGet($uri, $token);
        $response->assertStatus(200);
        $response->assertSuccessful();


        $response = $this->logout($token);
        $response->assertStatus(200);
        $response->assertSuccessful();


        // ... folytasd a tesztet
    }


}
