<?php

namespace Tests\Feature;

use App\Enums\UserTypes;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use withFaker, RefreshDatabase;

    public function testUserIndexGetSuccessfully()
    {
        $password = $this->faker->password;

        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ])
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $response = $this->json('GET', '/api/v1/users', [], $headers);
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testUserCreateValidationFailed()
    {
        $password = $this->faker->password;

        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ]),
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $data = [];
        $response = $this->json('POST', '/api/v1/users', $data, $headers);
        $response->assertStatus(400)
            ->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function testUserCreateSuccessfully()
    {
        $password = $this->faker->password;

        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ]),
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $password = $this->faker->password;
        $data = [
            'user_type' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $response = $this->json('POST', '/api/v1/users', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testUserShowSuccessfully()
    {
        $password = $this->faker->password;
        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ])
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $user = User::factory()->create();
        $data = [];
        $response = $this->json('GET', "/api/v1/users/{$user->id}", $data, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testUserUpdateValidationFailed()
    {
        $password = $this->faker->password;

        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ]),
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $user = User::factory()->create();
        $data = [];
        $response = $this->json('PUT', "/api/v1/users/{$user->id}", $data, $headers);

        $response->assertStatus(400)
            ->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function testUserUpdateSuccessfully()
    {
        $password = $this->faker->password;

        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ]),
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $password = $this->faker->password;
        $data = [
            'user_type' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->json('POST', '/api/v1/users', $data, $headers);
        $response = json_decode($response->getContent());

        $password = $this->faker->password;
        $data = [
            'user_type' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $response = $this->json('PUT', "/api/v1/users/{$response->data->user->id}", $data, $headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testUserDeleteSuccessfully()
    {
        $password = $this->faker->password;
        $admin = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::admin,
                ]),
            ]);

        $loginData = [
            'email' => $admin->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $user = User::factory()->create();

        $response = $this->json('DELETE', "/api/v1/users/{$user->id}", [], $headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }
}
