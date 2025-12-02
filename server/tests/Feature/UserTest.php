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
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $uri = '/api/users/login';
        $data = [
            'email' => $email,
            'password' => $password
        ];
        //Bejelentkezés
        $response = $this
            ->withHeaders($headers)
            ->postJson($uri, $data);

        //$token = $response->json('data')['token'];
        // dd($token);
        // return $token;
        return $response;
    }

    private function logout($token)
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        $uri = '/api/users/logout';

        $response = $this
            ->withHeaders($headers)
            ->postJson($uri);
        return $response;
    }


    public function test_create_delete_user(): void
    {

        $data = [
            'name' => 'Tanuló 3',
            'email' => 'tanulo3@example.com',
            'password' => '123',
        ];
        $uri = '/api/users';

        $response = $this
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->postJson($uri, $data);
        // dd($response);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => $data['email']]);

        $user = User::where('email', $data['email'])->first();
        $this->assertNotNull($user);

        //user törlés, ez így tiltott
        $id = $response->json('data')['id'];
        $uri = "/api/users/$id";

        $response = $this
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->deleteJson($uri);
            
        $response->assertStatus(401);                
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
        $response = $this->login($email, $password);

        //Lekérdezem, hogy a válasz státusza 200-e    
        $response->assertStatus(200);
        //Kiolvasom a válaszból a tokent
        $token = $response->json('data')['token'];
        //Ha van token, az jó
        $this->assertNotNull($token);


        $headersWithToken = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        //Egy nem védett útvonalra küldünk egy kérést
        $uri = '/api/sports';
        $response = $this
            ->withHeaders($headersWithToken)
            ->get($uri);

        // Ellenőrizzük, hogy a kérés sikeres volt-e
        $response->assertStatus(200);

        //Egy védett útvonalra küldünk kérést
        $uri = '/api/users';
        $response = $this
            ->withHeaders($headersWithToken)
            ->get($uri);

        // Ellenőrizzük, hogy a kérés el lett-e utasítva
        $response->assertStatus(403);

        //Logout
        $response = $this->logout($token);
        $response->assertStatus(200);
    }
}
