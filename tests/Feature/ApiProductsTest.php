<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiProductsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_access_protected_api(): void
    {
        $this->getJson('/api/products')->assertStatus(401);
        $this->postJson('/api/products', [])->assertStatus(401);
    }

    public function test_list_products_authenticated(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $p = Product::factory()->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonFragment(['name' => $p->name]);
    }

    public function test_create_update_delete_product(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'name' => 'Test Product',
            'price' => 123.45,
            'description' => 'A product for testing',
            'image_url' => 'https://example.com/img.png',
        ];

        $create = $this->postJson('/api/products', $payload)
            ->assertCreated()
            ->json();

        $this->assertArrayHasKey('id', $create);
        $id = $create['id'];

        $updatePayload = [
            'name' => 'Updated Name',
            'price' => 99.99,
            'description' => 'Updated description',
            'image_url' => 'https://example.com/updated.png',
        ];

        $this->putJson("/api/products/{$id}", $updatePayload)
            ->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name']);

        $this->deleteJson("/api/products/{$id}")
            ->assertOk()
            ->assertJson(['message' => 'Product deleted successfully']);
    }
}
