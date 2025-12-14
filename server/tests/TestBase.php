<?php

namespace Tests;

abstract class TestBase extends TestCase
{

    protected function login(string $email = "admin@example.com", string $password = "123")
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

        return $response;
    }


    protected function logout($token)
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

    protected function myGetToken($response)
    {
        $token = $response->json('data')['token'];
        return $token;
    }

    protected function myGetId($response)
    {
        $token = $response->json('data')['id'];
        return $token;
    }

    protected function myGet(string $uri, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        $response = $this
            ->withHeaders($headers)
            ->get($uri);
        return $response;    
    }

    protected function myPost(string $uri, array $data, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        
        $response = $this
            ->withHeaders($headers)
            ->postJson($uri, $data); // postJson() az adatok JSON-ként való küldéséhez
            
        return $response;
    }

    protected function myPatch(string $uri, array $data, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        
        $response = $this
            ->withHeaders($headers)
            ->patchJson($uri, $data); // patchJson() az adatok JSON-ként való küldéséhez
            
        return $response;
    }

    protected function myDelete(string $uri, string $token)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
        
        $response = $this
            ->withHeaders($headers)
            ->delete($uri); // delete() metódus használata
            
        return $response;
    }

    // Ide jöhetnek más közös segédfüggvények is (pl. CRUD metódusok)
}