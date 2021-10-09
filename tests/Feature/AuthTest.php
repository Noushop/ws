<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
  use RefreshDatabase;

  private $userStructure = ['name', 'email', 'role', 'created_at', 'updated_at', 'id'];

  public function test_just_the_two_of_us()
  {
    $response = $this->postJson(
      '/api/public/auth/just-the-two-of-us',
      [
        'name' => 'testUser',
        'email' => 'test@email.test',
      ]
    );

    $response->assertStatus(201)
      ->assertJsonStructure(['status', 'message', 'user' => $this->userStructure])
      ->assertJson([
        'status' => true,
        'message' => 'User successfully registered',
        'user' => [
          'name' => 'testUser',
          'email' => 'test@email.test',
          'role' => 'super-admin',
        ]
      ]);
  }

  public function test_login()
  {
    $user = User::factory()->create();
    $response = $this->actingAs($user, 'api')
      ->postJson('/api/public/auth/login', [
        'email' => 'test@mail.test',
        'password' => 'password',
      ]);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'status',
        'message',
        'data' => ['access_token', 'token_type', 'expires_in', 'user' => $this->userStructure],
      ]);
  }

  public function test_logout()
  {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->postJson('/api/public/auth/logout', []);

    $response->assertStatus(200)
      ->assertJsonStructure(['status', 'message']);
  }

  public function test_user_profile()
  {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->getJson('/api/public/auth/user-profile', []);

    $response->assertStatus(200)
      ->assertJsonStructure(['status', 'message', 'user' => $this->userStructure]);
  }
}
