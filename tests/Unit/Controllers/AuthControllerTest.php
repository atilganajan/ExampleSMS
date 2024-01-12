<?php

namespace Tests\Unit\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User successfully registered']);

        $this->assertDatabaseHas('users', ['username' => 'testuser']);
    }

    public function test_login()
    {
         User::create([
            'username' => 'testuser',
            'password' => bcrypt('testpassword'),
        ]);

        $loginData = [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_logout()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);

        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'User logged out']);
    }

}
