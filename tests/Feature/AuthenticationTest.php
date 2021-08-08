<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use withFaker, RefreshDatabase;

    public function testAuthLoginSuccessfully()
    {
        $user = User::factory()
            ->create();

        $loginData = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testAuthLogoutSuccessfully()
    {
        $user = User::factory()
            ->create();

        $loginData = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());

        $token = $response->data->token;

        $headers = ['Authorization' => "Bearer {$token}"];

        $this->json('POST', 'api/v1/auth/logout', [], $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                "message"
            ]);
    }
}
