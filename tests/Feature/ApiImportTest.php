<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiImportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_import_all_products_uses_http_fake_and_persists(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'fakestoreapi.com/products' => Http::response([
                [
                    'id' => 1,
                    'title' => 'Foo',
                    'price' => 10.5,
                    'description' => 'Foo desc',
                    'category' => 'cat-a',
                    'image' => 'https://example.com/a.png',
                ],
                [
                    'id' => 2,
                    'title' => 'Bar',
                    'price' => 20.75,
                    'description' => 'Bar desc',
                    'category' => 'cat-b',
                    'image' => 'https://example.com/b.png',
                ],
            ], 200),
        ]);

        $this->postJson('/api/import/all')
            ->assertOk()
            ->assertJsonFragment(['message' => 'Import completed successfully'])
            ->assertJsonStructure(['imported', 'skipped']);
    }

    public function test_import_specific_product_uses_http_fake_and_persists(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'fakestoreapi.com/products/99' => Http::response([
                'id' => 99,
                'title' => 'Baz',
                'price' => 30.99,
                'description' => 'Baz desc',
                'category' => 'cat-c',
                'image' => 'https://example.com/c.png',
            ], 200),
        ]);

        $this->postJson('/api/import/99')
            ->assertOk()
            ->assertJsonFragment(['message' => 'Product imported successfully'])
            ->assertJsonStructure(['product' => ['id', 'name', 'price', 'description', 'image_url']]);
    }
}
