<?php

namespace Tests\Feature;

use App\Enums\UserTypes;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class PostTest extends TestCase
{
    use withFaker, RefreshDatabase;

    public function testPostIndex()
    {
        Post::factory(10)->create();

        $password = $this->faker->password;

        $manager = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
                ])
            ]);

        $loginData = [
            'email' => $manager->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $response = $this->json('GET', '/api/v1/posts', [], $headers);
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testPostCreateValidationFailed()
    {
        $password = $this->faker->password;
        $user = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
                ])
            ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];
        $data = [];
        $response = $this->json('POST', '/api/v1/posts', [], $headers);
        $response->assertStatus(400)
            ->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function testPostCreateSuccessfully()
    {
        $password = $this->faker->password;
        $user = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
                ])
            ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];
        $data = [
            'title' => $this->faker->name,
            'content' => $this->faker->email,
            'slug' => $this->faker->slug
        ];
        $response = $this->json('POST', '/api/v1/posts', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testPostShowSuccessfully()
    {
        $password = $this->faker->password;
        $user = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
                ])
            ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $post = Post::withoutEvents(function () use ($user) {
            return Post::factory()->create([
                'created_by' => $user->id,
            ]);
        });
        $data = [];

        $response = $this->json('GET', "/api/v1/posts/{$post->id}", $data, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testPostUpdateValidationFailed()
    {
        $password = $this->faker->password;
        $user = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
                ])
            ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $post = Post::withoutEvents(function () use ($user) {
            return Post::factory()->create([
                'created_by' => $user->id,
            ]);
        });

        $data = [];
        $response = $this->json('PUT', "/api/v1/posts/{$post->id}", $data, $headers);
        $response->assertStatus(400)
            ->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function testPostUpdateSuccessfully()
    {
        $password = $this->faker->password;
        $user = User::factory()
            ->create([
                'password' => bcrypt($password),
                'role_id' => Role::factory()->create([
                    'name' => UserTypes::getUserTypesArray()[mt_rand(0, count(UserTypes::getUserTypesArray()) - 1)],
                ])
            ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->json('POST', '/api/v1/auth/login', $loginData);
        $response = json_decode($response->getContent());
        $token = $response->data->token;
        $headers = ['Authorization' => "Bearer {$token}"];

        $post = Post::withoutEvents(function () use ($user) {
            return Post::factory()->create([
                'created_by' => $user->id,
            ]);
        });

        $data = [
            'title' => $this->faker->name,
            'content' => $this->faker->email,
            'slug' => $this->faker->slug
        ];
        $response = $this->json('PUT', "/api/v1/posts/{$post->id}", $data, $headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    public function testPostDeleteSuccessfully()
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

        $post = Post::factory()->create();
        $response = $this->json('DELETE', "/api/v1/posts/{$post->id}", [], $headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);
    }
}
