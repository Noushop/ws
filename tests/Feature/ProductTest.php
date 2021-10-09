<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{
  use RefreshDatabase;
  private $productStructure = ['barcode', 'name', 'price', 'cost', 'quantity', 'created_at', 'updated_at'];

  public function test_get_many()
  {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    Product::factory(3)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->getJson('/api/private/products', []);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => ['*' => $this->productStructure],
        'current_page',
        'first_page_url',
        'from',
        'last_page',
        'last_page_url',
        'links' => ['*' => ['active', 'label', 'url']],
        'next_page_url',
        'path',
        'per_page',
        'prev_page_url',
        'to',
        'total',
      ]);
  }

  public function test_get_one()
  {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $product = Product::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->getJson('/api/private/product/' . $product->barcode, []);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'status',
        'message',
        'product' => $this->productStructure,
      ]);
  }

  public function test_create()
  {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->postJson('/api/private/product/create', [
        'barcode' => '100000000000001',
        'name' => 'test product',
        'price' => 100,
        'cost' => 99,
        'quantity' => 101,
      ]);

    $response->assertStatus(201)
      ->assertJsonStructure([
        'status',
        'message',
        'product' => $this->productStructure,
      ]);
  }

  public function test_update()
  {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $product = Product::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->putJson('/api/private/product/update/' . $product->barcode, [
        'barcode' => '100000000000001',
        'name' => 'test product',
        'price' => 100,
        'cost' => 99,
        'quantity' => 101,
      ]);

    $response->assertStatus(201)
      ->assertJsonStructure([
        'status',
        'message',
      ]);
  }
}
