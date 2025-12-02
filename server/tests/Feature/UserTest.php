<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{

    use DatabaseTransactions;
    //A bejelentkezést elintéző függvény
    private function login(string $email = "admin@example.com", string $password = "123")
    {
        //Bejelentkezés
        $response = $this
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->postJson('/api/users/login', [
                'email' => $email,
                'password' => $password
            ]);

        $token = $response->json('data')['token'];
        // dd($token);
        return $token;
    }

    public function test_login()
    {

        //Csinálok egy user-t
        $name = 'test';
        $email = 'test@example.com';
        $password = '123';

        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        //Loginolok a user-el
        $uri = '/api/users/login';
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $data = [
            'email' => $email,
            'password' => $password
        ];
        $response = $this
            ->withHeaders($headers)
            ->postJson($uri, $data);

        //Lekérdezem, hogy a válasz státusza 200-e    
        $response->assertStatus(200);
        //Kiolvasom a válaszból a tokent
        $token = $response->json('data')['token'];
        //Ha van token, az jó
        $this->assertNotNull($token);


        //Egy védett útvonalra küldünk egy kérést
        $response = $this
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])
            ->get('/api/users');

        // Ellenőrizzük, hogy a kérés sikeres volt-e
        $response->assertStatus(200);
    }
}
